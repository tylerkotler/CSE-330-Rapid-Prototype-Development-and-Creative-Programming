<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Display The Article</title>
</head>

<body>
<!-- page that displays a specific article -->
    <div class="logoutbutton">
        <form action="News.php" method="POST">
            <input type="submit" class="button" value="Go Back" />
        </form>
    </div><br><br>
    <div class="articledisplay">
        <?php
        require 'Database.php';
        session_start();
        //takes in the article's title from the News page
        $title = (string) $_POST["Title"];
        echo "<h1> $title </h1>";
        //query to get info from stories given the specific name of the article
        $stmt = $mysqli->prepare("select story_text, username, story_link, story_id from stories where story_name = '$title'");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        } 
        $stmt->execute(); 
        $stmt->bind_result($story_text, $username, $story_link, $story_id);
        
        while ($stmt->fetch()) {
            echo "<h2> By $username </h2>";
            if ($story_link != NULL) {
                //link to the external article on a button that says "here"
                //if statements run through the different cases of how a link was inputted and format it correctly
                // (IE: could have been amazon.com, www.amazon.com, or http://www.amazon.com)
                if (strcmp(substr($story_link, 0, 4), "http") == 0) {
                    echo "<form name='input' action='$story_link' target='_blank' method='get'>
                        <label> To view the full story on the website, click </label>
                     <input type='submit' class='herebutton' value='Here' />
                     <input type='hidden' name='token' value='".$_SESSION['token']."'>
                     </form>
                      ";
                } else if (strcmp(substr($story_link, 0, 4), "www.") == 0) {
                    echo "<form name='input' action='http://$story_link' target='_blank' method='get'>
                        <label> To view the full story on the website, click </label>
                     <input type='submit' class='herebutton' value='Here' />
                     <input type='hidden' name='token' value='".$_SESSION['token']."'>
                     </form>
                        ";
                } else {
                    echo "<form name='input' action='http://www.$story_link' target='_blank' method='get'>
                    <label> To view the full story on the website, click </label>
                    <input type='submit' class='herebutton' value='Here' />
                    <input type='hidden' name='token' value='".$_SESSION['token']."'>
                    </form>
                    ";
                }
            }
            //prints out the article
            echo "<div class='newsdiv'>";
            printf(
                "%s",
                htmlspecialchars($story_text)
            );
            echo "</div>";
        }
        //checks if someone is logged in, and if so, displays a comment box to upload a comment
        if ($_SESSION["user"] != NULL) {
            echo "<br><br>";
            echo "<form action = Comment.php method = 'POST'>
            <label> Comment: </label><br>
            <textarea class = 'textarea' id = 'commentinput' name='comment' style='width:500px;height:60px;'></textarea><br><br>
            <input type = 'hidden' name ='story_id' value='$story_id'/>
            <input type='submit' class='button' name ='submit' value='Upload'/>
            <input type='hidden' name='token' value='".$_SESSION['token']."'>
            </form>";
        }
        //query to get info from the comments table
        $stmt2 = $mysqli->prepare("select username, comment, story_id, comment_id from comments");
        if (!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt2->execute();
        $stmt2->bind_result($username, $comment, $id, $comment_id);

        echo "<h2 class='comments'> Comments </h2>";
        echo "<ol>\n";
        while ($stmt2->fetch()) {
            //checks if the id of the story to be displayed matches that from the query
            if ($id == $story_id) {
                //displays all the comments
                echo "<li> $comment </li>
                    \t <div class='small'> by $username </div>";
                //checks if the username of the comment is the same as the session user, and if so,
                //it displays a delete and edit comment button for the user
                if (strcmp($_SESSION["user"], $username) == 0) {
                    echo "
                        <form style='display: inline' action='DeleteComment.php' method='POST'>
                        <input type = 'hidden' name ='comment_id' value=$comment_id/>
                        <input type='submit' class = 'deletebutton' name ='deletecomment' value='Delete' />
                        <input type='hidden' name='token' value='".$_SESSION['token']."'>
                        </form>
                        <form style='display: inline' action='EditComment.php' method='POST'>
                        <input type = 'hidden' name ='comment_id' value=$comment_id/>
                        <input type='submit' class = 'deletebutton' name ='editcomment' value='Edit' />
                        <input type='hidden' name='token' value='".$_SESSION['token']."'>
                        </form>";
                }
            }
        }
        ?>
    </div>
</body>

</html>