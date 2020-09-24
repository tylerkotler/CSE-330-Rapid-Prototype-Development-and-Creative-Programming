<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Edit your comment</title>
</head>

<body>
    <?php
    require 'Database.php';

    session_start();

    $comment_id = (int) $_POST["comment_id"];
    //query to get the info from a comment given its id
    $stmt = $mysqli->prepare("select story_id, comment from comments where comment_id = $comment_id");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($story_id, $comment);
    while ($stmt->fetch()) {
    }
    $stmt->close();

    //query to get the name of the story given its id
    $stmt2 = $mysqli->prepare("select story_name from stories where story_id = $story_id");
    if (!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt2->execute();
    $stmt2->bind_result($story_name);
    while ($stmt2->fetch()) {
    }
    $stmt2->close();
    echo
        //button to go back to the news page
        //form that displays the current comment on the article to be edited
        //in the text box and sends the information to the uploadeditcomment page to have
        //the user's changes upload
        "<div class='logoutbutton'>
        <form action='DisplayArticle.php' method='POST'>
            <input type='hidden' name='Title' value='$story_name'>
            <input type='submit' class='button' value='Go Back' /><br>
        </form>
        </div><br>
        <h1> Edit Your Comment! </h1> 
        <div class = 'newsdiv'>
        <form action = UploadEditComment.php method = 'POST'>
            <label> Comment: </label><br>
            <textarea id = 'commentinput' name='comment' style='width:500px;height:60px;'>$comment</textarea><br><br>
            <input type = 'hidden' name = 'story_id' value=$story_id'/>
            <input type = 'hidden' name ='comment_id' value='$comment_id'/>
            <input type='submit' class='button' name ='submit' value='Upload'/>
            <input type='hidden' name='token' value='".$_SESSION['token']."'>
        </div>
        </form>"; 
    ?>
</body> 

</html>