<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Story to Database</title>
</head>

<body>
    <?php
    require 'Database.php';
    //uploads a new story
    session_start();

    $title = $mysqli->real_escape_string( $_POST["title"]);
    $article = $mysqli->real_escape_string($_POST["article"]);
    $link = $mysqli->real_escape_string( $_POST["link"]);

    $username = $_SESSION["user"];
    //sets a condition to false that will be used later when checking if the article title already exists
    $bool = FALSE;
    //query to select all titles from the stories table
    $stmt = $mysqli->prepare("select story_name from stories");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    //runs through the names of stories to see if the one being uploaded by the user already exists
    //if so, it sets the condition to true
    while ($row = $result->fetch_assoc()) {
        if (strcmp($row["story_name"], $title) == 0) {
            $bool = TRUE;
        }
    }
    $stmt->close(); 
    //if the title already exists, it sends the user to the same title php page
    if ($bool == TRUE) {
        header("Location: SameTitle.php");
    } 
    //if not, it inserts the new story into the stories table
    else {
        $stmt2 = $mysqli->prepare("insert into stories (username, story_name, story_text, story_link) values ('$username', '$title', '$article', '$link')");
        if (!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        } 
        $stmt2->execute();
        $stmt2->close();
        header("Location: News.php");
    }
    ?>
</body>

</html>