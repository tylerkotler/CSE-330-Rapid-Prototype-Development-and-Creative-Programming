<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Upload Edited Comment to Database</title>
</head>
 
<body> 
    <?php
    require 'Database.php';
    //uploads a comment after it has been edited
    session_start();

    $story_id = (int) $_POST["story_id"];
    $comment_id = (int) $_POST["comment_id"];
    $comment = $mysqli->real_escape_string($_POST["comment"]);

    $username = $_SESSION["user"];
    //query to update the comment in the comments table that has been edited
    $stmt2 = $mysqli->prepare("update comments set comment = '$comment' where comment_id = $comment_id");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->close();
    //query to get the name of the story given the story id from the comemnt
    $stmt = $mysqli->prepare("select story_name from stories where story_id = $story_id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($story_name);
    while ($stmt->fetch()) {
        //displays a button to continue back to the article after comment was edited
        echo "
            <h1> Congratulations, your comment has been edited! </h1>
            <div class='continuebutton'>
            <form style='font-size: 15px' action='DisplayArticle.php' method='POST'>
            <input type = 'hidden' name = 'Title' value='$story_name'>
            <input type='submit' class ='continuebutton' name ='go back' value='Continue' />
            </form>
            </div>
           ";
    }
    $stmt->close();

    ?>
</body>

</html>