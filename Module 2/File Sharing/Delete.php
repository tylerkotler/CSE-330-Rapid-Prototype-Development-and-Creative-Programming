<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Deleting a New Document from Your Directory</title>
</head>
<body>
<?php
    session_start();
    ?>
    <!--This page selects what file to delete-->
    <form enctype="multipart/form-data" action="Deleter.php" method="POST">
	<p>
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<label for="deletefile_input">Choose a file to delete:</label> <input name="deletedfile" type="file" id="deletefile_input" />
	</p>
	<p>
        <input type="submit" value="Delete File" />
	</p>
</form>
<!--Go back button-->
    <form action="View.php" method="POST">
        <input type="submit" value="Go Back" />
    </form>
</body>
</html>