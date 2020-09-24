<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logging in</title>
</head>
<body>
    <?php
    //For logging a user in to make sure that it is a valid username
    //create a new session to check
   session_start();
   //initally set boolean to false to create a parameter needed for success
   $bool = FALSE;
   //Creating a session variable of the username entered on Login.php
   $username = (String)$_POST["user"];
   $_SESSION["user"]= $username;
   //create a variable with names in users.txt
   $h = fopen("/srv/users.txt", "r");
   //while loop iterating through all of the usernames
    while(!feof($h)) {
        $result = fgets($h);
        //if the variable entered is the same as one of the names
        if(trim($result)==$username) {
            //set boolean to true and break out
            $bool = TRUE;
        break;
        }
    }
    //echo $bool;

        if ($bool == TRUE){
            //able to view files if logged in
          header("Location: View.php");
        }
        else{
            //session ended and go back to logging in if not able to log in
          header("Location: Login.php");
          session_destroy();
        }
	?>
</body>
</html>