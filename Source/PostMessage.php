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

// Insert into table
if (isset($_REQUEST["submit"])){

	$subject = mysqli_real_escape_string($conn, $_REQUEST["subject"]);
	$body = mysqli_real_escape_string($conn, $_REQUEST["body"]);
	$audience = $_REQUEST["filter"];
	if (empty($_REQUEST["lat"]) or empty($_REQUEST["lng"])){
		$lat = 'null';
		$lng = 'null';
	} else {
		$lat = $_REQUEST["lat"];
		$lng = $_REQUEST["lng"];
	}

	if ($audience == "block"){
		// Retrieving block
		$block_query = "SELECT mblock FROM Members WHERE email='$email';";
		$block_query_result = mysqli_query($conn, $block_query);
		$block_result = mysqli_fetch_all($block_query_result, MYSQLI_ASSOC);
		$block = $block_result[0]["mblock"];
		mysqli_free_result($block_query_result);
		// insert query
		$insert_query = "INSERT INTO Messages(poster, audiences, audience_id, subjects, body, longitude, latitude) VALUES 
											 ('$email', '$audience', $block, '$subject', '$body', $lng, $lat);";
	} elseif ($audience == "hood"){
		// Retrieving hood
		$hood_query = "SELECT bhood FROM Members, Blocks WHERE email='$email' AND mblock=bid;";
		$hood_query_result = mysqli_query($conn, $hood_query);
		$hood_reuslt = mysqli_fetch_all($hood_query_result, MYSQLI_ASSOC);
		$hood = $hood_reuslt[0]["bhood"];
		mysqli_free_result($hood_query_result);
		// insert query
		$insert_query = "INSERT INTO Messages(poster, audiences, audience_id, subjects, body, longitude, latitude) VALUES
											 ('$email', '$audience', $hood, '$subject', '$body', $lng, $lat);";
	} elseif ($audience == "person"){
		// Retrieving memid
		$person = mysqli_real_escape_string($conn, $_REQUEST["person"]);
		$memid_query = "SELECT memid FROM Members WHERE email='$person';";
		$memid_query_result = mysqli_query($conn, $memid_query);
		$memid_result = mysqli_fetch_all($memid_query_result, MYSQLI_ASSOC);
		$person_id = $memid_result[0]["memid"];
		mysqli_free_result($memid_query_result);
		// insert query
		$insert_query = "INSERT INTO Messages(poster, audiences, audience_id, subjects, body, longitude, latitude) VALUES
											 ('$email', '$audience', $person_id, '$subject', '$body', $lng, $lat);";
	} else {
		$insert_query = "INSERT INTO Messages(poster, audiences, subjects, body, longitude, latitude) VALUES
											 ('$email', '$audience', '$subject', '$body', $lng, $lat);";
	}

	if (mysqli_query($conn, $insert_query)){
		header("Location: HomePage.php");
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
	<title>Post Message</title>
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
		<br><br>Post New Message<br><br>
		<form>
			Subject:<br>
			<input type="text" name="subject" placeholder="subject" size="50%"><br><br>
			Body Text:<br>
			<textarea name="body" rows="10" cols="100"></textarea><br><br>
			Audience:<br>
			<input type="radio" name="filter" value="friends" > Friends
			<input type="radio" name="filter" value="neighbors"> Neighbors
			<input type="radio" name="filter" value="block"> Block
			<input type="radio" name="filter" value="hood"> Neighborhood
			<input type="radio" name="filter" value="person"> Person <br><br>
			Specify email:<br>
			<input type="email" name="person" placeholder="Required if Person was selected" size="40"><br><br>
			Location:<br>
			Latitude: <input type="number" name="lat" placeholder="optional" step="any">
			Longitude: <input type="number" name="lng" placeholder="optional" step="any">
			<br><br>
			<input type="submit" name="submit" value="Post">

		</form><br>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>