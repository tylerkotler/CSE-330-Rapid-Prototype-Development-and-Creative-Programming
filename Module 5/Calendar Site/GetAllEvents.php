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
$user_id = $mysqli->real_escape_string($json_obj['user_id']);
$logged_in_user_id = $_SESSION['user_id'];
if(strcmp($logged_in_user_id,$user_id)!=0){
	die("Attack detected");
}



//array to hold all of the events
$events_arr = array();
$arrayCounter = 0;

//gets all events from the events table that were created by the user
$stmt = $mysqli->prepare("select * from events where user_id = $logged_in_user_id");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Attack detected"
    ));
    exit;
}
$stmt->execute(); 
$stmt->bind_result($event_id, $user_id, $title, $time, $month, $day, $year, $description, $tag, $tag_enable);

while ($stmt->fetch()) {
    //adds all info into an event array
    $event = array(
        "sharer" => "",
        "event_id" => htmlentities($event_id),
        "title" => htmlentities($title),
        "time" => htmlentities($time),
        "month" => htmlentities($month),
        "day" => htmlentities($day),
        "year" => htmlentities($year),
        "description" => htmlentities($description),
        "tag" => htmlentities($tag),
        "tag_enable" =>htmlentities($tag_enable)
    );
    //adds each event into an array of all of the events to be sent back
    $events_arr[$arrayCounter] = $event;
    $arrayCounter++;
}
$stmt->close();


//this part gets all of the events/calendars that are shared with the user and adds them
//into the events array before sending it back

//arrays to hold all the info from the share table
$usernames = array();
$user_ids = array();
$event_ids = array();
$shareCounter = 0;

//gets info from the share table
$stmt2 = $mysqli->prepare("select user_id, event_id from share where usertoshare_id = $logged_in_user_id");
if (!$stmt2) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Attack detected"
    ));
    exit;
}
$stmt2->execute(); 
$stmt2->bind_result($userid, $eventid);
//adds all user and event ids to arrays
while($stmt2->fetch()){
    $user_ids[$shareCounter] = $userid;
    $event_ids[$shareCounter] = $eventid;
    $shareCounter++;
}
$stmt2->close();

//gets info from users table
//adds all usernames to arrays based on user ids
for($i=0; $i<$shareCounter; $i++){
    $userrid = $user_ids[$i];
    $stmt3 = $mysqli->prepare("select username from users where user_id = $userrid");
    if (!$stmt3) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        echo json_encode(array(
            "success" => false,
            "message" => "Attack detected"
        ));
        exit;
    }
    $stmt3->execute(); 
    $stmt3->bind_result($username);
    while($stmt3->fetch()){
        $usernames[$i] = $username;
    }
}

//for each share, gets the events
for($i=0; $i<$shareCounter; $i++){
    $userrrid = $user_ids[$i];
    $eventttid = $event_ids[$i];
    //in this case, id of 0 means the entire calendar of a user is shared with the logged in
    //user, so all events of that user are selected
    if($eventttid==0){
        $stmt4 = $mysqli->prepare("select * from events where user_id = $userrrid");
        if (!$stmt4) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            echo json_encode(array(
                "success" => false,
                "message" => "Attack detected"
            ));
            exit;
        }
        $stmt4->execute(); 
        $stmt4->bind_result($event_id, $user_id, $title, $time, $month, $day, $year, $description, $tag, $tag_enable);
        while ($stmt4->fetch()) {
            //adds all info from each event in the shared calendar into an event array
            $username2 = $usernames[$i];
            $event = array(
                "sharer" => htmlentities($username2),
                "event_id" => htmlentities($event_id),
                "title" => htmlentities($title),
                "time" => htmlentities($time),
                "month" => htmlentities($month),
                "day" => htmlentities($day),
                "year" => htmlentities($year),
                "description" => htmlentities($description),
                "tag" => htmlentities($tag),
                "tag_enable" => htmlentities($tag_enable)
            );
            //adds each event into an array of events
            $events_arr[$arrayCounter] = $event;
            $arrayCounter++;
        }
        $stmt4->close();
    }
     //in this case, the id is not 0 which means the a specific event is shared with the logged in
    //user, so that event is selected
    else{
        $stmt4 = $mysqli->prepare("select * from events where event_id = $eventttid");
        if (!$stmt4) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            echo json_encode(array(
                "success" => false,
                "message" => "Attack detected"
            ));
            exit;
        }
        $stmt4->execute(); 
        $stmt4->bind_result($event_id, $user_id, $title, $time, $month, $day, $year, $description, $tag, $tag_enable);
    
        while ($stmt4->fetch()) {
            //adds all info from the event into an event array
            $username2 = $usernames[$i];
            $event = array(
                "sharer" => htmlentities($username2),
                "event_id" => htmlentities($event_id),
                "title" => htmlentities($title),
                "time" => htmlentities($time),
                "month" => htmlentities($month),
                "day" => htmlentities($day),
                "year" => htmlentities($year),
                "description" => htmlentities($description),
                "tag" => htmlentities($tag),
                "tag_enable" => htmlentities($tag_enable)
            );
            //adds each event into an array of events
            $events_arr[$arrayCounter] = $event;
            $arrayCounter++;
        }
        $stmt4->close();
    }
}
echo json_encode($events_arr);
exit;
?>