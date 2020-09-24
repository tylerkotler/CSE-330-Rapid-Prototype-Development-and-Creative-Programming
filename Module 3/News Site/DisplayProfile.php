<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Display The Profile</title>
</head>

<body>
    <div class="logoutbutton">
        <form action="AllProfiles.php" method="POST">
            <input type="submit" class="button" value="Go Back" />
        </form>
    </div><br>
    <div class="articledisplay">
        <?php
        require 'Database.php';
        session_start();
        $username = (string) $_POST["Title"];
        echo "<h1> $username's profile </h1>";
        $stmt = $mysqli->prepare("select story_name from stories where username = '$username'");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $stmt->bind_result($story_name);
        echo "<h2 class='comments'> Articles: </h2>";
        echo "<ol>\n";
        while ($stmt->fetch()) {
            echo "<li> $story_name </li>";
        }
        echo "</ol>";
        $stmt->close();

        $stmt2 = $mysqli->prepare("select stories.story_name, comment, stories.username from comments join stories on (stories.story_id = comments.story_id) where comments.username = '$username'");
        if (!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt2->execute();
        $stmt2->bind_result($story_name, $comment, $user);
        echo "<h2 class='comments'> Comments: </h2>";
        echo "<ol>\n";
        while ($stmt2->fetch()) {
            echo "<li> $comment </li>
                    \t <div class='small'> On: $story_name </div>
                    \t <div class='small'> By $user </div><br>";
        }
        echo "</ol>"; 
        $stmt2->close(); 

        $stmt3 = $mysqli->prepare("select stories.story_name, stories.likes, stories.username from likes join stories on (stories.story_id = likes.story_id) where likes.username = '$username'");
        if (!$stmt3) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt3->execute();
        $stmt3->bind_result($story_name, $likes, $user);
        echo "<h2 class='comments'> Likes: </h2>";
        echo "<ol>\n";
        while ($stmt3->fetch()) {
            echo "<li> $story_name </li>
                \t <div class='small'> By $user </div>
                \t <div class='small'> $likes likes </div><br>";
        }
        echo "</ol>";
        $stmt3->close();
        ?>
    </div>
</body>

</html>