<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File uploading</title>
</head>
<body>
<?php
    //page that checks to make sure that a file can be uploaded
    session_start();

    // Get the filename and make sure it is valid
    $filename = basename($_FILES['uploadedfile']['name']);
    if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
        echo htmlentities("Invalid filename");
        header("Location: View.php");
        exit;
    }
    
    // Get the username and make sure it is valid 
    //already done with check.php
    // $username = $_SESSION['name'];
    // if( !preg_match('/^[\w_\-]+$/', $username) ){
    //     echo "Invalid username";
    //     exit;
    // }
    
    //if the filename is valid the file is uploaded. You are taken to view all of the files regardless
    $full_path = sprintf("/srv/uploads/%s/%s", $_SESSION["user"], $filename);
    if( move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path) ){
        header("Location: View.php");
        exit;
    }else{
        header("Location: View.php");
        exit;
    }
    ?> 
</body>
</html>