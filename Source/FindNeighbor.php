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

// Retrieving all block member
$bm_query = "SELECT m2.memid as memid, m2.firstname AS fname, m2.lastname AS lname, m2.email AS email FROM Members m1, Members m2 
			 WHERE m1.email='$email' AND m1.mblock = m2.mblock AND m1.memid != m2.memid;";
$bm_query_result = mysqli_query($conn, $bm_query);
$bm_result = mysqli_fetch_all($bm_query_result, MYSQLI_ASSOC);

// Retriving all neighbors
$neighbor_query = "SELECT neighbor, year(since) as since FROM Neighbor WHERE memail = '$email';";
$neighbor_query_result = mysqli_query($conn, $neighbor_query);
$neighbor_result = mysqli_fetch_all($neighbor_query_result, MYSQLI_ASSOC);
$neighbors = array();
foreach ($neighbor_result as $neighbor) {
	$neighbors[$neighbor["neighbor"]] = $neighbor["since"];
}

// Clean up
mysqli_free_result($bm_query_result);
mysqli_free_result($neighbor_query_result);
mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Add New Neighbor</title>
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
		<a href="HomePage.php" style="float: left">Back</a>
		<a href="Logout.php" style="float: right;">Log Out</a>
		<br><br>
		Members in your block: <br><br>
		<?php  
			foreach ($bm_result as $member) {
				echo "<div style='border-style: outset; width:50%; margin:auto'><br>";
		?>
				<a href="UserProfile.php?from=neighbor&id=<?php echo $member['memid']?>"><?php echo $member["fname"]." ".$member["lname"]; ?></a>
		<?php
				if (array_key_exists($member["email"], $neighbors)){
					echo "<br><br>";
					echo "your neighbor since ".$neighbors[$member["email"]];
					echo "<br><br>";
				} else {
					echo "<br><br>";
					echo "<a href='AddNeighbor.php?neighbor=" . $member["email"] . "'>Add</a>";
					echo "<br><br>";
				}
				echo "</div><br>";
			}
		?>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>