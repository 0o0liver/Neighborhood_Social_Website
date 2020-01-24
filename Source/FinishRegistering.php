<?php  
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
} else {
	header('Location:LoginPage.php');
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
			background-image: url('img/background.jpg');
			background-size: cover;
			background-attachment: fixed;
			background-repeat: no-repeat;
		}
	</style>
</head>
<body>
	<div style="background-color: white; border-radius: 8px; border-style: outset; width: 50%; position: absolute; 
		top: 50%; left: 50%; transform: translate(-50%, -65%)">
		<h2>Congratulation!</h2>
		The registration process is now completed. <br><br>
		Thank you for signing up for our services. <br><br>
		Welcome to our community!!! <br><br>
		Click <a href="LoginPage.php">here</a> to log in. <br><br>
	</div>
</body>
</html>