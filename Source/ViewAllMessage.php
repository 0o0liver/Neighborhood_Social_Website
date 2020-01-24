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

// Filtering
if (isset($_REQUEST["submit"])){
	// Header
	$checked = $_REQUEST["filter"];

	// Fill display message array
	if (empty($checked)){
		//
	} else {
		if ($checked == "all"){
			$message_query = "(SELECT * FROM Messages WHERE poster = '$email') 
								UNION
							  (SELECT * FROM Messages WHERE audiences='block' AND audience_id = (SELECT mblock FROM Members WHERE email='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'hood' AND audience_id = (SELECT bhood FROM Blocks b, Members m WHERE b.bid = m.mblock AND m.email='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'friends' AND poster IN (SELECT friend_2 AS f FROM Friends WHERE friend_1='$email' UNION SELECT friend_1 AS f FROM Friends WHERE friend_2='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'neighbors' AND poster IN (SELECT memail FROM Neighbor WHERE neighbor='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'person' AND audience_id = (SELECT memid FROM Members WHERE email = '$email'));";
		} elseif ($checked == "hood") {
			$message_query = "SELECT * FROM Messages WHERE audiences = 'hood' AND audience_id = 
							 (SELECT bhood FROM Blocks b, Members m WHERE b.bid = m.mblock AND m.email='$email');";
		} elseif ($checked == "block") {
			$message_query = "SELECT * FROM Messages WHERE audiences='block' AND audience_id = (SELECT mblock FROM Members WHERE email='$email');";
		} elseif ($checked == "neighbors") {
			$message_query = "SELECT * FROM Messages WHERE audiences = 'neighbors' and poster in (select memail from Neighbor where neighbor='$email');";
		} elseif ($checked == "friends") {
			$message_query = "SELECT * FROM Messages WHERE audiences = 'friends' AND poster IN 
							 (SELECT friend_2 AS f FROM Friends WHERE friend_1='$email' UNION
							  SELECT friend_1 AS f FROM Friends WHERE friend_2='$email');";
		} elseif ($checked == "person") {
			$message_query = "SELECT * FROM Messages WHERE audiences = 'person' and audience_id = (select memid from Members where email = '$email');";
		} elseif ($checked == "unread") {
			$message_query = "(SELECT * FROM Messages WHERE poster = '$email' AND mesid NOT IN (SELECT mesid FROM Read_Messages WHERE memail = '$email')) 
								UNION
							  (SELECT * FROM Messages WHERE audiences='block' AND mesid NOT IN (SELECT mesid FROM Read_Messages WHERE memail = '$email') AND audience_id = (SELECT mblock FROM Members WHERE email='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'hood' AND mesid NOT IN (SELECT mesid FROM Read_Messages WHERE memail = '$email') AND audience_id = (SELECT bhood FROM Blocks b, Members m WHERE b.bid = m.mblock AND m.email='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'friends' AND mesid NOT IN (SELECT mesid FROM Read_Messages WHERE memail = '$email') AND poster IN (SELECT friend_2 AS f FROM Friends WHERE friend_1='$email' UNION SELECT friend_1 AS f FROM Friends WHERE friend_2='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'neighbors' AND mesid NOT IN (SELECT mesid FROM Read_Messages WHERE memail = '$email') AND poster IN (SELECT memail FROM Neighbor WHERE neighbor='$email'))
								UNION
							  (SELECT * FROM Messages WHERE audiences = 'person' AND mesid NOT IN (SELECT mesid FROM Read_Messages WHERE memail = '$email') AND audience_id = (SELECT memid FROM Members WHERE email = '$email'))";
		} elseif ($checked == "my_post") { 
			$message_query = "SELECT * FROM Messages WHERE poster = '$email';";
		} else {
			$message_query = "SELECT * FROM Messages WHERE post_time > (SELECT logout_time FROM Login_Info WHERE memail = '$email' ORDER BY logout_time DESC limit 1 offset 0);";
		}

		$message_query_result = mysqli_query($conn, $message_query);
		$message_result = mysqli_fetch_all($message_query_result, MYSQLI_ASSOC);
		foreach ($message_result as $message) {
			$messages[$message["subjects"]] = $message["mesid"];
		}
		mysqli_free_result($message_query_result);
	}
}

// Cleaning up
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
	<title>View All Messages</title>
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
		View All Messages<br><br>
		<form method="POST" action="ViewAllMessage.php">
			<input type="radio" name="filter" value="all" checked> All 
			<input type="radio" name="filter" value="hood"> Hood
			<input type="radio" name="filter" value="block"> Block
			<input type="radio" name="filter" value="neighbors"> Neighbors
			<input type="radio" name="filter" value="friends"> Friends
			<input type="radio" name="filter" value="person"> Personal 
			<input type="radio" name="filter" value="unread"> Unread 
			<input type="radio" name="filter" value="my_post"> My Post
			<input type="radio" name="filter" value="new"> New<br><br>
			<input type="submit" name="submit" value="filter"><br><br>
		</form>
		<?php  
			foreach (array_keys($messages) as $message) {
				echo "<div style='border-style: outset; width:50%; margin:auto'>";
		?>
			<a href="MessagePage.php?from=view&id=<?php echo $messages[$message]; ?>"><?php echo $message ?></a>
		<?php
				echo "</div><br>";
			}
		?>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>

</body>
</html>