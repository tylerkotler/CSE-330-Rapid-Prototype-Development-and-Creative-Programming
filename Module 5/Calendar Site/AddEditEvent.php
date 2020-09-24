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
$title = $mysqli->real_escape_string($json_obj['evtitle']);
$time = $mysqli->real_escape_string($json_obj['evtime']);
$description = $mysqli->real_escape_string($json_obj['evdescription']);
$tag = $mysqli->real_escape_string($json_obj['evtag']);
$event_id = $mysqli->real_escape_string($json_obj['ev_id']);
$user_id = $mysqli->real_escape_string($json_obj['user_id']);
$day = $mysqli->real_escape_string($json_obj['evday']);
$month = $mysqli->real_escape_string($json_obj['evmonth']);
$year = $mysqli->real_escape_string($json_obj['evyear']);

//checks to ensure user from client side is the same as the session user
//updates events table with the edited event
if(strcmp($_SESSION['user_id'],$user_id)==0){
    $stmt = $mysqli->prepare("update events set title='$title', time='$time', month=$month, day=$day, year=$year, description='$description', tag = '$tag' where event_id = $event_id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        echo json_encode(array(
		    "success" => false,
		    "message" => "Event not edited - incorrectly filled at least one text box"
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