<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet"
    type="text/css" href="Style.css">
    <title>Create a New User</title>
</head>
<body> 
    <!--Form to sign up for a new user. Goes to create.php where it will check if it is already a user-->
    <p style="font-weight: bold;">Register as a new user now!</p>
        <form action="Create.php" method="POST">
                <label >Enter New Username:<input id = "usernameinput" name ="user"></label>
                <input type="submit" class="button" name = "submit" value="Register" />
        </form><br>
        <!--Go back to login.php if dont want to create user-->
        <form action="Login.php" method="POST">
            <input type="submit" class="button" value="Go Back" />
        </form>                        
</body>
</html>