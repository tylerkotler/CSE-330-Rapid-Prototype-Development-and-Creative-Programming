<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Creating New User</title>
</head>

<body>
    <?php 
    require 'Database.php';
    //page that creates a new user
    session_start();
    //Exact same thing as logging in
    //Creating variables of the first & last name, username, password entered on Login.php
    $firstname = (string) $_POST["firstname"];
    $lastname = (string) $_POST["lastname"];
    $username = (string) $_POST["user"];
    $password = (string) $_POST["password"];
    $password = password_hash($password, PASSWORD_DEFAULT);
    $_SESSION["user"] = $username;
    $bool = FALSE;
    //create a variable with names in users table
    $stmt = $mysqli->prepare("select username from users");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();

    //checks if the username already exists in the users table
    while ($row = $result->fetch_assoc()) {
        if (strcmp($row["username"], $username) == 0) {
            $bool = TRUE;
        }
    }
    $stmt->close();
    //if the username does not exist, the new info gets added to the users table
    if ($bool == FALSE) {
        $stmt2 = $mysqli->prepare("insert into users (first_name, last_name, username, password) values ('$firstname', '$lastname', '$username', '$password')");
        if (!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt2->bind_param('ssss', $first_name, $last_name, $username, $password);
        $stmt2->execute();
        $stmt2->close();

        header("Location: Login.php");
    } else {
        header("Location: Register.php");
    }
    ?>
</body>

</html>