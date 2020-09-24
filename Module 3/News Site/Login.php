<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Log in a user or create new one</title>
</head>

<body>
<!-- login page -->
    <br> 
    <h1><b>JT News Site</b></h1>
    <div class="firstdiv">
    <!-- form to enter username and password and send info to check php -->
        <form action="Check.php" method="POST">
            <label>Username: <input id="usernameinput" name="user"></label><br><br>
            <label>Password: <input type="password" id="passwordinput" name="password"></label><br><br>
            <input type="submit" class="button" name="submit" value="Log In" />
        </form><br>
        <p> New user? </p>
        <!-- button to register as a new user -->
        <form action="Register.php" method="POST">
            <input type="submit" class="button" name="submit" value="Register" /><br>
        </form>
        <p> Or continue without logging in </p>
        <!-- button to continue to the news page without logging in - no session user will be created -->
        <form action="News.php" method="POST">
            <input type="submit" class="button" name="submit" value="View News" /><br>
        </form>
    </div>
    <?php
    session_start();
    // session user starts as null, changes if user logs in
    $_SESSION["user"] = null;
    ?>
</body>

</html>