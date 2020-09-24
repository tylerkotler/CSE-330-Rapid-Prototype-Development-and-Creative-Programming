<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Add a Comment to Database</title>
</head>

<body>
    <?php
    require 'Database.php';

    session_start();
    //page that uploads a new comment
    $comment = $mysqli->real_escape_string($_POST["comment"]);
    $username = $_SESSION["user"];
    $id = $_POST["story_id"];

    //query to insert comment info 
    $stmt2 = $mysqli->prepare("insert into comments (story_id, comment, username) values ($id, '$comment', '$username')");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->close();

    //query to pull the title of the article with a specific ID
    $stmt = $mysqli->prepare("select story_name from stories where story_id = $id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($story_name); 
    //Button to go back to the Display Article page passing the story title info
    while ($stmt->fetch()) { 
        echo "
            <h1> Congratulations, your comment has been uploaded! </h1>
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