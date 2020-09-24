<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Add A New Article</title>
</head>
 
<body>
    <?php
    session_start();
    ?>
    <div class="logoutbutton">
        <form action="News.php" method="POST">
            <input type="submit" class="button" name="submit" value="Go Back" /><br>
        </form>
    </div><br>
    <h1> Type/Link an Article! </h1>
    <div class="adddiv">
         <!-- Form that takes in article info and sends it to Upload story -->
        <form action=UploadStory.php method="POST">form
            <label> Title: </label><br>
            <input id="titleinput" name="title" style="width:500px;height:20px"><br><br>
            <label> Article/Summary: </label><br>
            <textarea class="textarea" id="articleinput" name="article" style="width:500px;height:280px;"></textarea><br><br>
            <label> Link to External Article (Optional): </label><br>
            <input id="linkinput" name="link" style="width:500px;height:20px"><br><br>
            <input type="submit" class="button" name="submit" value="Upload" />
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    </div>
    </form>
    <br>
</body>

</html>