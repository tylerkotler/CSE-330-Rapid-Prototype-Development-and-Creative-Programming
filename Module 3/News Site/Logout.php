<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log Out a User</title>
</head>

<body>
    <?php 
    //session ended and go back to Login if person wants to log out
    session_start();
    if (isset($_SESSION['user'])) {
        session_destroy();
        echo "<script>location.href='Login.php'</script>";
    } else {
        echo "<script>location.href='Login.php'</script>";
    }
    ?>
</body>

</html>