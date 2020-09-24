<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">  
    <link rel="stylesheet"
    type="text/css" href="Style.css">
    <title>Log a User in</title>
</head>
<body>
    <!--Creates a form to log in. Links to check.php to see if its a valid user-->
        <h3><b>Log In To Share A File</b></h3><br>
        <form action="Check.php" method="POST">
                <label >Username:<input id = "usernameinput" name ="user"></label>
                <input type="submit" class="button" name = "submit" value="Log In" />
        </form>
        <!--Register link to sign up for file sharing site-->
        <form action="Register.php" method="POST">
                <input type="submit" class="button" name = "submit" value="Register" /><br>
        </form>
</body> 
</html> 