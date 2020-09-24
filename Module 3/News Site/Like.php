<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Like an article</title>
</head>

<body>

</html>
<?php

require 'Database.php';
session_start();
$username = $_SESSION["user"];
$story_id = (int) $_POST["story_id"];
$likes = (int)$_POST["likes"];

$stmt3 = $mysqli->prepare("select * from likes");
if (!$stmt3) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt3->execute();
$stmt3->bind_result($userswholiked, $story_ids);
$bool = TRUE;
if ($stmt3 != NULL) { 
    while ($stmt3->fetch()) {
        if (strcmp($_SESSION["user"], $userswholiked) == 0 && $story_id == $story_ids) {
            $bool = FALSE;
        }
    }
}
$stmt3->close();
if ($bool == TRUE) {

    $stmt = $mysqli->prepare("insert into likes (username, story_id) values ('$username', $story_id)");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $stmt->close();
    $likes = $likes+1;
    $stmt2 = $mysqli->prepare("update stories set likes = $likes where story_id = '$story_id'");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->close();

    header("Location: News.php");
} else {
    header("Location: News.php");
}
?>

</html>