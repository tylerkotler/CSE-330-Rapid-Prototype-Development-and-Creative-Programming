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
$user_id = $mysqli->real_escape_string($json_obj['user_id']);
$event_id = $mysqli->real_escape_string($json_obj['event_id']);

$logged_in_user_id = $_SESSION['user_id'];
//checks to ensure user from client side is the same as the session user
//deletes event from events table
if(strcmp($logged_in_user_id, $user_id)==0){
    $stmt = $mysqli->prepare("delete from events where event_id = $event_id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute(); 
    $stmt->close();
    $stmt2 = $mysqli->prepare("delete from share where event_id = $event_id");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute(); 
    $stmt2->close();
    echo json_encode(array("success" => true));
	exit;
} 
else {
    echo json_encode(array(
		"success" => false,
		"message" => "Attack detected"
    ));
    exit;
}
