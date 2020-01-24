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
$messages = array();

// Search
if (isset($_REQUEST["search"])){
	$keyword = mysqli_real_escape_string($conn, $_REQUEST["keyword"]);
	$search_query = "SELECT * FROM Messages WHERE body LIKE '%$keyword%';";
	$search_query_result = mysqli_query($conn, $search_query);
	$search_result = mysqli_fetch_all($search_query_result, MYSQLI_ASSOC);
	foreach ($search_result as $message) {
		$messages[$message["subjects"]] = $message["mesid"];
	}
	mysqli_free_result($search_query_result);
}

// Cleaning up
mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Search Message</title>
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
		<br><br>Search Messages<br><br>
		<form>
			<input type="text" name="keyword" placeholder="keyword" size="50%"><br><br>
			<input type="submit" name="search" value="Search"><br><br>
		</form>
		<?php  
			foreach (array_keys($messages) as $message) {
				echo "<div style='border-style: outset; width:50%; margin:auto'>";
		?>
			<a href="MessagePage.php?from=search&id=<?php echo $messages[$message]; ?>"><?php echo $message ?></a>
		<?php
				echo "</div><br>";
			}
		?>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>