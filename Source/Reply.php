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
$previous = htmlspecialchars($_GET["from"]);
$type = htmlspecialchars($_GET["to"]);
$reply_to = htmlspecialchars($_GET["id"]);

// Insert reply 
if (isset($_REQUEST["submit"])){

	$body = mysqli_real_escape_string($conn, $_REQUEST["reply"]);

	if ($type == "msg"){
		$query = "INSERT INTO Msg_Reply(replier, body, reply_to) VALUES ('$email', '$body', $reply_to)";
	} else {
		$query = "INSERT INTO Reply_Reply(replier, body, reply_to) VALUES ('$email', '$body', $reply_to)";
	}
	if (mysqli_query($conn, $query)){
		header("Location: MessagePage.php?from=$mfrom&id=$previous");
	} else {
		echo mysqli_error($conn);
	}
}

// Cleaning up
mysqli_close($conn);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Reply</title>
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
		<a href="MessagePage.php?from=homepage&id=<?php echo $previous ?>" style="float: left;">Back</a>
		<a href="Logout.php" style="float: right;">Log Out</a>
		<br><br>Reply<br><br>
		<form method="POST", action="Reply.php?from=<?php echo $previous?>&to=<?php echo $type?>&id=<?php echo $reply_to?>">
			<textarea rows="10" cols="100" name="reply"></textarea>
			<br><br>
			<input type="submit" name="submit" value="reply" >
		</form><br>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>