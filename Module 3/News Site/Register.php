<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="Style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <title>Register New User</title>
</head>

<body>
<!-- page to register a new user -->
<!-- button to go back to the log in page -->
    <div class="logoutbutton"> 
        <form action="Login.php" method="POST">
            <input type="submit" class="button" value="Go Back" />
        </form>
    </div><br>
    <h1><b>Register as a new user!</b></h1>
    <div class="firstdiv">
    <!-- form to create a new user that takes in name, username, and password info -->
        <form action="Create.php" method="POST">
            <label>First Name: <input id="firstnameinput" name="firstname"></label><br><br>
            <label>Last Name: <input id="lastnameinput" name="lastname"></label><br><br>
            <label>New Username: <input id="usernameinput" name="user"></label><br><br>
            <label>New Password: <input id="passwordinput" name="password"></label><br><br>
            <input type="submit" class="button" name="submit" value="Register" />
        </form><br><br>
        <!--Go back to login.php if dont want to create user-->
    </div>
</body>

</html>