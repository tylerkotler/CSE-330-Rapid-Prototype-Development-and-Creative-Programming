<?php
//some code taken/adapted from course wiki
require 'Database.php';

header("Content-Type: application/json"); 

session_start();
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

if($json_obj['token'] !== $_SESSION['token']){
    die("Invalid Token");
}

$username = $mysqli->real_escape_string($json_obj['username']);
$password = $mysqli->real_escape_string($json_obj['password']);

$password = password_hash($password, PASSWORD_DEFAULT);

//selects all usernames from the users table
$stmt = $mysqli->prepare("select username from users");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->execute();
$result = $stmt->get_result();

$bool = FALSE;
//if the username already exists in the table, sets the boolean to true
while ($row = $result->fetch_assoc()) {
    if (strcmp($row["username"], $username) == 0) {
        $bool = TRUE;
    }
}
$stmt->close();
//if the username does not exist, the new info gets added to the users table
if ($bool == FALSE) {
    $stmt2 = $mysqli->prepare("insert into users (username, password) values ('$username', '$password')");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
    }
    $stmt2->bind_param('ss', $username, $password);
    $stmt2->execute();
    $stmt2->close();

    
    echo json_encode(array("success" => true));
	exit;
} 
else {
    echo json_encode(array(
		"success" => false,
		"message" => "Username already exists"
    ));
    exit;
}
?>