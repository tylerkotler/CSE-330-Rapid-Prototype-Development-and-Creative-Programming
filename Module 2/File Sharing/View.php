<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View your Documents</title>
    <link rel="stylesheet"
     type="text/css" href="Style.css">
</head> 
<body>
    <!--Page lists all of the files, and has the actions to do others-->
    <h3><b>Your Files:</b></h3><br><br>

    <!--Buttons to add a file-->
    <form action="Add.php" method="POST">
        <input type="submit" class="button" value="Add File" />
    </form>
  
 <br>
<div class="filesdiv">
<?php 
    session_start(); //show the files that the user upload
        if($_SESSION["user"]!=null ){  
                
                  
                //create a list with all of the files and buttons to delete or view file underneath
            $fileDir = sprintf("/srv/uploads/%s", $_SESSION["user"]);
            $dir=opendir($fileDir);
             if(is_dir($fileDir)==TRUE){
                    while((($fname=readdir($dir))==TRUE)){
                        if(($fname != ".") && ($fname != "..")){
                            
                            echo 
                            "<p class='test'><font size='4'>$fname</font>
                            <form action='Show_file.php' method='POST'>
                            <input type='hidden' name ='fname' value='$fname'/>
                            <input class='button' type='submit' value='View File' />
                            </form>
                            <form action='Deleter.php' method='POST'>
                            <input type='hidden' name='MAX_FILE_SIZE' value='20000000' />
                            <input type = 'hidden' name ='fname' value='$fname'/>
                            <input name='deletedfile' class='button' type='submit' id='deletedfile'  value='Delete File'/>
                            </form>
                            </p>";
                        
                       }    
                    }
            }
        }
        else{
            header("Location: Login.php");
        }
         
            ?>
</div>
 <br><br><br>
<!--Log out button-->
 <form action="Logout.php" method="POST">
            <input type="submit" class="button" value="Log Out" />
    </form>
    <br><br><br>
</body>
</html> 