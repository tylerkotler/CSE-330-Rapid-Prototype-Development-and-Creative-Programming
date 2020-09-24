<?php
require 'Database.php';
header("Content-Type: application/json"); 

session_start();

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

if($json_obj['token'] !== $_SESSION['token']){
    die("Invalid Token");
}

$prevuseragent = @$_SESSION['useragent'];
$curruseragent = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $prevuseragent !== $curruseragent){
		die("Attack detected!");
}
else{
	$_SESSION['useragent'] = $curruseragent;
}
//extracts data from fetch
$usertoshare = $mysqli->real_escape_string($json_obj['usertoshare']);

$user_id = $mysqli->real_escape_string($json_obj['user_id']);
$logged_in_user_id = $_SESSION['user_id'];
if(strcmp($logged_in_user_id,$user_id)!=0){
	die("Attack detected");
}
//selects the user id from the users table where the username is the current user
$stmt = $mysqli->prepare("select user_id from users where username = '$usertoshare'");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
        "success" => false,
        "message" => "No user of username: $usertoshare"
    ));
    exit;
}
$stmt->execute();
$stmt->bind_result($usertoshare_id);
while ($stmt->fetch()) {
}
$stmt->close();
//creates a new share in the share table with the user's id and the id of 
//the user to share your calendar with
$stmt2 = $mysqli->prepare("insert into share (user_id, usertoshare_id) values ($logged_in_user_id, $usertoshare_id)");
if (!$stmt2) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt2->execute();
$stmt2->close();

echo json_encode(array(
    "success" => true,
));
exit;


?>