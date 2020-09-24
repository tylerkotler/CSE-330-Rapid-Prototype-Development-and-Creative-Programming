<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Delete a Comment</title>
</head>

<body>
    <?php
    //Deletes a comment
    require 'Database.php';

    session_start();

    $comment_id = (int) $_POST["comment_id"];

    //query to get the story id of the comment that is being deleted
    $stmt2 = $mysqli->prepare("select story_id from comments where comment_id = $comment_id");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->bind_result($story_id);
    while ($stmt2->fetch()) {
    }
    $stmt2->close();

    //query to delete the specific comment
    $stmt = $mysqli->prepare("delete from comments where comment_id = $comment_id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    //query to use the story id to get the name of the story from stories table
    $stmt2 = $mysqli->prepare("select story_name from stories where story_id = $story_id");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->bind_result($story_name); 
    //Button to go back to Display Article page that passes the story name through the form
    while ($stmt2->fetch()) {
        echo "
            <h1> Your comment has been deleted. </h1>
            <div>
            <form style='font-size: 15px' action='DisplayArticle.php' method='POST'>
            <input type = 'hidden' name = 'Title' value='$story_name'>
            <input type='submit' class ='button' name ='go back' value='Continue' />
            </form>
            </div>
            ";
    }
    ?>
</body>

</html>