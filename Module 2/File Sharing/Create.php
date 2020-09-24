<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php
    //page that creates a new user
    session_start();
      //Exact same thing as logging in
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
   
           if ($bool == FALSE){//if user doesn't exist
               //add users to both the txt file and create a directory
               file_put_contents("/srv/users.txt", $username, FILE_APPEND);
               file_put_contents("/srv/users.txt", "\n", FILE_APPEND);
                $path = sprintf("/srv/uploads/%s",$username);
                mkdir($path, 0777);
                header("Location: Login.php");
           }
           else{
               //go back to logging in if not able to create user in
             header("Location: Register.php");
           }
  ?>
</body>
</html>