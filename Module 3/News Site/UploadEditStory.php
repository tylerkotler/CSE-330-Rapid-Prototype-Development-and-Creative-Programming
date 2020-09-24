<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Edited Story to Database</title>
</head>

<body>  
    <?php
    require 'Database.php';
    //uploads a story after it has been edited
    session_start();

    $title = $mysqli->real_escape_string($_POST["title"]);
    $article = $mysqli->real_escape_string($_POST["article"]);
    $link = $mysqli->real_escape_string($_POST["link"]);
    $id = (int) $_POST["story_id"];

    $username = $_SESSION["user"];
    //query to update the stories table to the new edited version of the story, title, and link
    $stmt2 = $mysqli->prepare("update stories set story_name = '$title', story_text = '$article', story_link = '$link' where story_id = $id;");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->close();
    header("Location: News.php");
    ?>
</body>

</html>