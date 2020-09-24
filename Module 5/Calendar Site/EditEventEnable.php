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
$tag_enable = $mysqli->real_escape_string($json_obj['event_tag_enable']);
$event_id = $mysqli->real_escape_string($json_obj['event_id']);
$user_id = $mysqli->real_escape_string($json_obj['user_id']);
//checks to ensure user from client side is the same as the session user
//updates the status of the tag (enabled or disabled) for the specific event in the 
//events table
if(strcmp($_SESSION['user_id'],$user_id)==0){
    $stmt = $mysqli->prepare("update events set tag_enable = '$tag_enable' where event_id = $event_id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        echo json_encode(array(
		    "success" => false,
		    "message" => "Tag enable not changed"
	    ));
	    exit;
    }
    $stmt->execute();
    $stmt->close();

    echo json_encode(array(
        "success" => true,
    ));
    exit;
}
else{
    echo json_encode(array(
        "success" => false,
        "message" => "Attack detected"
    ));
    exit;
}

?>