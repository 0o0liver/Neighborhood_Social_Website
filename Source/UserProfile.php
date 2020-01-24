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
$id = htmlspecialchars($_GET['id']);
$from = htmlspecialchars($_GET['from']);

// Retrieving member info
$query = "SELECT * FROM Members WHERE memid = $id;";
$query_result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

// Cleaning up
mysqli_free_result($query_result);
mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<title>User Profile</title>
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
		<?php  
			if ($from == 'neighbor'){
				echo "<a href='FindNeighbor.php' style='float :left;'>Back</a>";
			} elseif ($from == 'friend'){
				echo "<a href='FindFriend.php' style='float :left;'>Back</a>";
			} else{
				echo "<a href='HomePage.php' style='float :left;'>Back</a>";
			}
		?>
		<a href="Logout.php" style="float: right;">Log Out</a>
		<br><br>User Profile<br><br>
		<img src="<?php echo $result[0]['profile_pic'] ?>" style="width: 100px; height: 100px; "><br><br>
		Name: <?php echo $result[0]["firstname"]." ".$result[0]["lastname"] ?><br><br>
		Phone: <?php echo $result[0]["phone"] ?><br><br>
		Email: <?php echo $result[0]["email"] ?><br><br>
		Address: <?php echo $result[0]["street"] ?><br><br>
		City: <?php echo $result[0]["city"] ?><br><br>
		State: <?php echo $result[0]["state"] ?><br><br>
		<script type="text/javascript">
			sessionStorage.setItem("address", '<?php echo $result[0]['address'] ?>');
			sessionStorage.setItem("city", '<?php echo $result[0]['city'] ?>');
			sessionStorage.setItem("state", '<?php echo $result[0]['state'] ?>');
		</script>
		<iframe src="map.html"></iframe><br><br>
		Description:<br>
		<div style="background-color: white; border-radius: 8px; border-style: outset; width: 50%; margin: auto; padding: 10px">
		<?php  
			if (empty($result[0]["descriptions"])){
				echo "Not Available.";
			} else{
				echo $result[0]["descriptions"];
			}
		?>
		</div>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>

</body>
</html>