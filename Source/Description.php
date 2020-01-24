<?php  

if (isset($_REQUEST["submit"])){
	// checking if it is empty inoput
	if (empty($_REQUEST["description"])){
		header('Location: Profile.php');
	} else {
		include('db_connect.php');

		// Gathering information
		$description = mysqli_real_escape_string($conn, $_REQUEST["description"]);

		// Construct Query
		$query = "Update"
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<style type="text/css">
		body{
			font-family: lucida grande; 
			text-align: center;
			color: black;
			background-image: url('img/Register.jpg');
			background-size: cover;
			background-attachment: fixed;
			background-repeat: no-repeat;
		}
	</style>
</head>
<body>
	<div style="background-color: white; border-radius: 8px; border-style: outset; width: 50%; position: absolute; 
		top: 50%; left: 50%; transform: translate(-50%, -65%)">
		<h2>Personal Description</h2>
		<form method="POST" action="Profile.php">
		<textarea rows="10" cols="100" name="description" placeholder="optional"></textarea><br><br>
		<input type="submit" name="submit" value="Next"><br><br>
		</form>
	</div>
</body>
</html>