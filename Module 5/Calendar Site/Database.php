<?php
//Database location that is called by all require statements on other php pages
$mysqli = new mysqli('localhost', 'tylerkotler', 'Vermont6', '330module5');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
 	?>