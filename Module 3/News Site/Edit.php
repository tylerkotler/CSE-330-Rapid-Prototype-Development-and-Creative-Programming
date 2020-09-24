<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Edit Existing Story</title>
</head>

<body> 
    <?php 
    require 'Database.php';

    session_start();
    //variables passed from News to get the info of the story
    $story_name = $_POST["story_name"];
    $story_text = $_POST["story_text"];
    $story_link = $_POST["story_link"];
    //query to get the story's id give it's name
    $stmt = $mysqli->prepare("select story_id from stories where story_name = '$story_name'");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($story_id);
    while ($stmt->fetch()) {
    }
    $stmt->close();
    
    echo
        //button to go back to the news page
        //form that displays the current story title, text, and link of the article to be edited
        //in the inputs/text boxes and sends the information to the uploadeditstory page to have
        //the user's changes upload
        "<div class='logoutbutton'>
        <form action='News.php' method='POST'>
            <input type='submit' class='button' name = 'submit' value='Go Back' /><br>
        </form>
        </div><br>
        <h1> Edit Existing Article! </h1> 
        <div class = 'adddiv'>
        <form action = UploadEditStory.php method = 'POST'>
            <label> Title: </label><br>
            <input value = '$story_name' id = 'titleinput' name = 'title' style='width:500px;height:20px'><br><br>
            <label> Article/Summary: </label><br>
            <textarea name = 'article' class='textarea'> $story_text</textarea><br><br>
            <label> Link to External Article (Optional): </label><br>
            <input value = '$story_link' id = 'linkinput' name ='link' style='width:500px;height:20px'><br><br>
            <input type = 'hidden' name ='story_id' value='$story_id'/>
            <input type='submit' class='button' name ='submit' value='Upload' />
        </div>
        </form>";
    ?>

</body>

</html>