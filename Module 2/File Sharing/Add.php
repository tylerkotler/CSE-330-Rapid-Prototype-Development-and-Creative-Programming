<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet"
    type="text/css" href="Style.css">
    <title>Adding a New Document to Your Directory</title>
</head>
<body>
    <?php
    session_start();
    ?>
    <!--This page selects what file to upload-->
    <form enctype="multipart/form-data" action="Uploader.php" method="POST">
	<p>
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<label for="uploadfile_input">Choose a file to upload:</label> <input name="uploadedfile" class="button" type="file" id="uploadfile_input" />
	</p> 
	<p>
        <input type="submit" class="button" value="Upload File" />
	</p>
</form>
<!--Go back button-->
    <form action="View.php" method="POST">
        <input type="submit" class="button" value="Go Back" />
    </form>
</body>
</html>