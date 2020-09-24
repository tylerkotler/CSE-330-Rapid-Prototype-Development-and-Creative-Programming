<?php
require 'Database.php';
header("Content-Type: application/json"); 

session_start();

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
//destroys the session upon log out
session_destroy();

echo json_encode(array(
    "success" => true,
));
exit;

?>