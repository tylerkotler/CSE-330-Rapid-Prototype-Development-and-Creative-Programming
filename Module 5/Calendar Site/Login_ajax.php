<?php

require 'Database.php';
header("Content-Type: application/json"); 

session_start();
ini_set("session.cookie_httponly", 1);

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

//extracts data from the fetch
$username = $mysqli->real_escape_string($json_obj['username']);
$password = $mysqli->real_escape_string($json_obj['password']);

$bool = FALSE;

//selects the user id and password from the users table where the username is the 
//same as the user entered in the form
$stmt = $mysqli->prepare("select user_id, password from users where username = '$username'");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->execute(); 
$stmt->bind_result($id, $pswd);

$user_id = -1;
while ($stmt->fetch()) {
	//checks to make sure password is valid - if so, sets the user id to the id associated with 
	//that user and sets the boolean to true
    if (password_verify($password, $pswd) == TRUE) {
		$bool = TRUE;
		$user_id = $id;
    }
}
$stmt->close();

if($bool==TRUE){
	//starts the session with respective session variables, sends back information on the logged
	//in user
	session_start();
	$_SESSION['username'] = $username;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 
	$_SESSION['user_id'] = $user_id;

	echo json_encode(array(
		"success" => true,
		"username" => "$username",
		"user_id" => "$user_id",
		"token" => "$_SESSION[token]"
	));
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
	));
	exit;
}
?>