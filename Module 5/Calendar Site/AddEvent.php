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
$tag_enable = $mysqli->real_escape_string($json_obj['evtag_enable']);
$users = $mysqli->real_escape_string($json_obj['evusers']);
$usersarr = array();
if(strcmp($users, "")!=0){
	$usersarr = explode(",", $users);
}
$user_id = $mysqli->real_escape_string($json_obj['user_id']);
$day = $mysqli->real_escape_string($json_obj['evday']);
$month = $mysqli->real_escape_string($json_obj['evmonth']);
$year = $mysqli->real_escape_string($json_obj['evyear']);

$logged_in_user_id = $_SESSION['user_id'];
if(strcmp($logged_in_user_id,$user_id)!=0){
	die("Attack detected");
}

//inserts new event into events table
$stmt = $mysqli->prepare("insert into events (user_id, title, time, month, day, year, description, tag, tag_enable) values ($user_id, '$title', '$time', $month, $day, $year, '$description', '$tag', '$tag_enable')");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Event not added - incorrectly filled at least one text box"
	));
	exit;
}
$stmt->execute();
$stmt->close();

//selects the event_id from the event just inserted into the table
$stmt2 = $mysqli->prepare("select max(event_id) from events");
if (!$stmt2) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Event not added"
	));
	exit;
}
$stmt2->execute();
$stmt2->bind_result($event_id);
while($stmt2->fetch()){}

//runs through each user in the array of users that the event is to be shared with
$length = count($usersarr);
for($i=0; $i<$length; $i++){
	$usertoshare = $usersarr[$i];
	//selects the user_ids of all the users from the users table
	$stmt4 = $mysqli->prepare("select user_id from users where username = '$usertoshare'");
	if (!$stmt4) {
		printf("Query Prep Failed: %s\n", $mysqli->error);
		echo json_encode(array(
			"success" => false,
			"message" => "No user of username: $usertoshare"
		));
		exit;
	}
	$stmt4->execute();
	$stmt4->bind_result($usertoshare_id);
	while($stmt4->fetch()){}

	//inserts into the share table the information of each share of the event
	$stmt3 = $mysqli->prepare("insert into share (user_id, usertoshare_id, event_id) values ($logged_in_user_id, $usertoshare_id, $event_id)");
	if (!$stmt3) {
		printf("Query Prep Failed: %s\n", $mysqli->error);
		echo json_encode(array(
			"success" => false,
			"message" => "Event not added"
		));
		exit;
	}
	$stmt3->execute();
	$stmt3->close();
}

echo json_encode(array(
    "success" => true,
));
exit;

?>