<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Check user after login</title>
</head>

<body> 
    <?php

    require 'Database.php';

    session_start();

    $username = (string) $_POST["user"];
    $password = (string) $_POST["password"];
    $_SESSION["user"] = $username;

    $bool = FALSE;

    $stmt = $mysqli->prepare("select password from users where username =?");
    $stmt->bind_param('s', $username);
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"]) == TRUE) {
            $bool = TRUE;
        }
    }
    $stmt->close();
    if ($bool == TRUE) {
        header("Location: News.php");
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    } else {
        header("Location: Login.php");
        session_destroy();
    }


    ?>
</body>

</html>