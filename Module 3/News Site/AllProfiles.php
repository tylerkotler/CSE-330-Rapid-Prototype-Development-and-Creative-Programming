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
    <div class="logoutbutton">
        <form action="News.php" method="POST">
            <input type="submit" class="button" value="Go Back" />
        </form>
    </div><br>
    <h1><b>All JT News Profiles:</b></h1>
    <?php
    session_start();
    ?>
    <div class="newsdiv">
        <?php
        require 'Database.php';
        // php query to select info from the users table
        $stmt = $mysqli->prepare("select first_name, last_name, username from users");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $stmt->bind_result($first_name, $last_name, $username);
        $full_name = $first_name . " " . $last_name;
        echo "<ol>\n";
        // button to click on the names to see their profile
        while ($stmt->fetch()) {
            echo "<li> <form style='font-size: 15px' action='DisplayProfile.php' method='POST'>
                    <input type='submit' class = 'articlebutton' name ='Title' value='$username' />
                    <input type='hidden' name='token' value='".$_SESSION['token']."'>
                    </form> 
                    \n
                    \t $first_name $last_name
                    </li>";
            echo "<br>";
        }
        echo "</ul>\n";

        $stmt->close();
        ?>
    </div>
</body>

</html>