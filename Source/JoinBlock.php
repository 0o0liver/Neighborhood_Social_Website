<?php  

// Check if user is logged in
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
} else {
	header('Location:LoginPage.php');
}

// Header
$email = $_SESSION["username"];
include('db_connect.php');

// Retrieving all blocks
$query = "SELECT hname, bname, bid FROM Hoods, Blocks WHERE hid=bhood AND 
			hcity=(SELECT city FROM Members WHERE email='$email') AND 
			hstate=(SELECT state FROM Members WHERE email='$email');";
$query_result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

// Cleaning up
mysqli_free_result($query_result);
mysqli_close($conn);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Join Block</title>
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
	<br><br><br><br><br><br>
	<div style="background-color: white; border-radius: 8px; border-style: outset; width: 50%; margin: auto; padding: 10px">
		<a href="HomePage.php" style="float: left;">Back</a>
		<a href="Logout.php" style="float: right;">Log Out</a>
		<br><br>
		All blocks in your city <br><br>
		<?php  
			foreach ($result as $block) {
				echo "<div style='border-style: outset; width:50%; margin:auto'><br>";
				echo $block["bname"].",<br>".$block["hname"]."<br><br>";
		?>
		<a href="JoinRequest.php?block=<?php echo $block['bid'] ?>">Request</a><br><br>
		<?php
				echo "</div><br>";
			}
		?>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>