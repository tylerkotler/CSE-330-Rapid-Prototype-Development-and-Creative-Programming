<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File deleting</title>
</head>
<body>
<?php
//page that deletes a file
    session_start();
    $finame = $_POST["fname"];
    // Get the filename and make sure it is valid
    $filename = basename($finame);
    if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
        echo htmlentities("Invalid filename");
        exit;
    }
    
    // Get the username and make sure it is valid 
    //already done with check.php
    // $username = $_SESSION['name'];
    // if( !preg_match('/^[\w_\-]+$/', $username) ){
    //     echo "Invalid username";
    //     exit;
    // }
    
    //if the file name is valid delete the file and take you back to View all of the files regardless
    $full_path = sprintf("/srv/uploads/%s/%s", $_SESSION["user"], $filename);
    echo "$full_path";
    if( unlink($full_path) ){
       header("Location: View.php");
        exit;
    }else{
       header("Location: View.php");
        exit;
    }
    ?> 
</body>
</html>