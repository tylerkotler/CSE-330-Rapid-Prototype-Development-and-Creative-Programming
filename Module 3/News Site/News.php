<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>News Site</title>
</head>

<body>
<!-- button to go back to the login in page and logs out the user -->
    <div class="logoutbutton">
        <form style='display: inline' action="Logout.php" method="POST">
            <input type="submit" class="button" value="Log Out" />
        </form>
    </div><br>
    <!-- button to view all the profiles of users of the news site -->
    <div class="viewbutton">
        <form style='display: inline' action="AllProfiles.php" method="POST">
            <input type="submit" class="button" value="View All Profiles" />
        </form>
    </div><br>
    <h1><b>JT News Site</b></h1>
    <?php
    session_start();
    if ($_SESSION["user"] != null) {
        //checks if there is a user logged in, and if so, displays the button to add a new article
        echo
            "<p>
        <form action='Add.php' method='POST'> 
        <input type='submit' class='button' value='Add Article'/> 
        </form>
        </p>";
    }
    ?>
    <div class="newsdiv">
        <?php
        require 'Database.php';
        //query to select the info from the stories table
        $stmt = $mysqli->prepare("select likes, story_id, story_name, username, story_text, story_link from stories");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $stmt->bind_result($likes, $story_id, $story_name, $username, $story_text, $story_link);

        echo "<ol>\n";
        while ($stmt->fetch()) {
            //lists all of the article titles as buttons to their respective articles
            echo "<li> <form style='font-size: 15px' action='DisplayArticle.php' method='POST'>
                    <input type='submit' class = 'articlebutton' name ='Title' value='$story_name' />
                    <input type='hidden' name='token' value='".$_SESSION['token']."'>
                    </form> \n
                    \t by $username 
                    </li>";
            //checks if there is a user logged in, and if so, displays the button to like an article
            if ($_SESSION["user"] != NULL) {
                echo "<form style='display: inline' action='Like.php' method='POST'>
                <input type = 'hidden' name ='story_id' value='$story_id'/>
                <input type = 'hidden' name = 'likes' value = $likes/>
                <input type='submit' class = 'deletebutton' name ='likearticle' value='Like ($likes)' />
                <input type='hidden' name='token' value='".$_SESSION['token']."'>
                </form>"; 
            } 
            //checks if the logged in user is the same as the specific stories username
            //if so, it displays buttons to edit and delete the story
            if (strcmp($_SESSION["user"], $username) == 0) {
                echo "
                <form style='display: inline' action='Delete.php' method='POST'>
                <input type = 'hidden' name ='story_name' value='$story_name'/>
                <input type = 'hidden' name ='story_id' value=$story_id/>
                <input type='submit' class = 'deletebutton' name ='deletearticle' value='Delete' />
                <input type='hidden' name='token' value='".$_SESSION['token']."'>
                </form>
                <form style='display: inline' action='Edit.php' method='POST'>
                <input type = 'hidden' name ='story_name' value='$story_name'/>
                <input type = 'hidden' name ='story_text' value='$story_text'/>
                <input type = 'hidden' name ='story_link' value='$story_link'/>
                <input type='submit' class = 'deletebutton' name ='editarticle' value='Edit' />
                <input type='hidden' name='token' value='".$_SESSION['token']."'>
                </form>";
            }
            echo "<br>";
        }
        echo "</ul>\n";

        $stmt->close();
        ?>
    </div>
</body>

</html>