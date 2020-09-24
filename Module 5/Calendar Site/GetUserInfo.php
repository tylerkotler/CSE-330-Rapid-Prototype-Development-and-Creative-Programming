<?php

require 'Database.php';
header("Content-Type: application/json"); 

session_start();

//checks for a session user id
//if there is one, then it calls to get the associated username and and passes back these values
//so the javascript page can update its global variables
if(isset($_SESSION['user_id'])){ 
    $stmt = $mysqli->prepare("select username from users where user_id = ?");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute(); 
    $stmt->bind_result($username);
    $stmt->fetch();
    echo json_encode(array(
        "success" => true,
        "user" => $username,
        "user_id" => $_SESSION['user_id'],
        "token" => $_SESSION['token']
    ));
    exit;
}
else{
    echo json_encode(array(
        "success" => false,
        "user" => "",
        "user_id" => -1,
        "token" => ""
    ));
    exit;
}
?>