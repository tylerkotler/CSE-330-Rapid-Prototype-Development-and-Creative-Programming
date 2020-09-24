<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Delete an Article</title>
</head>
 
<body>
    <?php
    //Deletes all info related to a specific story
    require 'Database.php';

    session_start();

    $story_name = (String)$_POST["story_name"];
    $story_id = (int)$_POST["story_id"];

    //Deletes all info from likes that are related to the story that is being delated
    $stmt3 = $mysqli->prepare("delete from likes where story_id = $story_id");
    if (!$stmt3) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt3->execute();
    $stmt3->close();
    
    //deletes all comments that are associated with the story that is getting deleted
    $stmt2 = $mysqli->prepare("delete from comments where story_id = $story_id");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->close();

    //deletes the story from the stories table
    $stmt = $mysqli->prepare("delete from stories where story_name = '$story_name'");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $stmt->bind_param($story_name);
    $stmt->close();
    header("Location: News.php");
    ?>
</body>

</html>