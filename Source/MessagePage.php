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
$id = htmlspecialchars($_GET["id"]);
$from = htmlspecialchars($_GET["from"]);

// Insert into Read_Message
$insert_query = "INSERT INTO Read_Messages(memail, mesid) VALUES ('$email', $id)";
if (mysqli_query($conn, $insert_query)){
	// success
} else {
	echo mysqli_error($conn);
}
// Retrieving message
$query = "SELECT * FROM Messages, Members WHERE mesid = $id AND poster = email";
$query_result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($query_result, MYSQLI_ASSOC);


// Reteriving message replies
$mr_query = "SELECT * FROM Msg_Reply WHERE reply_to = $id;";
$mr_query_result = mysqli_query($conn, $mr_query);
$mr_result = mysqli_fetch_all($mr_query_result, MYSQLI_ASSOC);

// Reteriving reply replies
$rr_query = "SELECT * FROM Reply_Reply WHERE reply_to IN (SELECT mrid FROM Msg_Reply WHERE reply_to = $id)";
$rr_query_result = mysqli_query($conn, $rr_query);
$rr_result = mysqli_fetch_all($rr_query_result, MYSQLI_ASSOC);

// Cleaning up
mysqli_free_result($query_result);
mysqli_free_result($mr_query_result);
mysqli_free_result($rr_query_result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Message Page</title>
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
		if ($from == "search"){
			echo "<a href='SearchMessage.php' style='float: left;''>Back</a>";
		} elseif ($from == "view"){
			echo "<a href='ViewAllMessage.php' style='float: left;''>Back</a>";
		} else{
			echo "<a href='HomePage.php' style='float: left;''>Back</a>";
		}
		?>
		<a href="Logout.php" style="float: right;">Log Out</a>
		<br><br>Message Detail<br><br>
		<div style="background-color: white; border-radius: 8px; border-style: outset; margin: auto; padding: 10px">
			Subject: <?php echo $result[0]["subjects"] ?><br><br>
			Poster: <?php echo $result[0]["firstname"]." ".$result[0]["lastname"] ?><br><br>
			Time: <?php echo $result[0]["post_time"] ?><br><br>
			Body:
			<div style="background-color: white; border-radius: 8px; border-style: outset; margin: auto; padding: 10px">
				<?php echo $result[0]["body"] ?>
			</div>
			<br>
			Location:
			<br>
			<script type="text/javascript">
				sessionStorage.setItem("lat",<?php echo $result[0]["latitude"] ?>);
				sessionStorage.setItem("lng",<?php echo $result[0]["longitude"] ?>);
			</script>
			<?php 
				if (empty($result[0]["latitude"]) || empty($result[0]["longitude"])){
					echo "Location not available for this message.";
				} else{
			?>
					<iframe src="MessageMap.html"></iframe>
			<?php
				}
			?>
			<br><br>
			<a href="Reply.php?from=<?php echo $id?>&to=msg&id=<?php echo $result[0]['mesid'] ?>">reply</a>
		</div>
		<br>Replies<br><br>
		<div style="background-color: white; border-radius: 8px; border-style: outset; margin: auto; padding: 10px">
			<?php  
				foreach ($mr_result as $mr) {
					echo "<div style='background-color: white; border-radius: 8px; border-style: outset; margin: auto; padding: 10px'>";
					echo "ref#: ".$mr["mrid"]."<br>".$mr["replier"]." to Original @ ". $mr["reply_time"].":<br><br>".$mr["body"];
			?>
					<br><br><a href="Reply.php?from=<?php echo $id?>&to=rly&id=<?php echo $mr["mrid"] ?>">reply</a>
			<?php
					echo "</div>";
				}
				foreach ($rr_result as $rr) {
					echo "<div style='background-color: white; border-radius: 8px; border-style: outset; margin: auto; padding: 10px'>";
					echo "ref#: ".$rr["rrid"]."<br>".$rr["replier"]." to reply#".$rr["reply_to"]." @ ".$rr["reply_time"].":<br><br>".$rr["body"];
			?>
					<br><br><a href="Reply.php?from=<?php echo $id?>&to=rly&id=<?php echo $rr["rrid"] ?>">reply</a>
			<?php
					echo "</div>";
				}
			?>
		</div>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>