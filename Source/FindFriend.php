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

// Retrieving all hood member
$query = "SELECT memid, firstname, lastname, email FROM Members m, Blocks b WHERE email != '$email' AND m.mblock = b.bid AND 
		  b.bhood = (SELECT b.bhood FROM Members m1, Blocks b WHERE email = '$email' AND m1.mblock = b.bid);";
$query_result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

// Retrieving all friends
$friend_query = "SELECT firstname, lastname, year(f.since) as since, email as friend FROM Members m, Friends f WHERE m.email = f.friend_1 AND friend_2='$email'
					UNION
		  		 SELECT firstname, lastname, year(f.since) as since, email as friend FROM Members m, Friends f WHERE m.email = f.friend_2 AND friend_1='$email';";
$friend_query_result = mysqli_query($conn, $friend_query);
$friend_result = mysqli_fetch_all($friend_query_result, MYSQLI_ASSOC);
$all_friends = array();
foreach ($friend_result as $friend) {
	$all_friends[$friend["friend"]] = $friend["since"];
}

// Retrieving all requested member
$requestee_query = "SELECT requestee, date(request_time) as time FROM Friend_Request WHERE requester = '$email' AND request_status='pending';";
$requestee_query_result = mysqli_query($conn, $requestee_query);
$requestee_result = mysqli_fetch_all($requestee_query_result, MYSQLI_ASSOC);
$all_requested = array();
foreach ($requestee_result as $requested) {
	$all_requested[$requested["requestee"]] = $requested["time"];
}

// Clean up
mysqli_free_result($query_result);
mysqli_free_result($friend_query_result);
mysqli_free_result($requestee_query_result);
mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Add New Friend</title>
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
		Members in your neighborhood
		<br><br>
		<?php  
			foreach ($result as $member) {
				echo "<div style='border-style: outset; width:50%; margin:auto'><br>";
		?>
				<a href="UserProfile.php?from=friend&id=<?php echo $member['memid']?>"><?php echo $member["firstname"]." ".$member["lastname"]; ?></a>
		<?php
				if (array_key_exists($member["email"], $all_friends)){
					echo "<br><br>";
					echo "your friend since ".$all_friends[$member["email"]];
					echo "<br><br>";
				} elseif (array_key_exists($member["email"], $all_requested)) {
					echo "<br><br>";
					echo "requested on ".$all_requested[$member["email"]];
					echo "<br><br>";
				} else {
					echo "<br><br>";
					echo "<a href='RequestFriend.php?requestee=" . $member["email"] . "'>Request</a>";
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