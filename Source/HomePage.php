<?php  
// Check if user is logged in
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
} else {
	header('Location:LoginPage.php');
}

// Header
$email = $_SESSION["username"];
$fname = $_SESSION["fname"];
$lname = $_SESSION["lname"];
include('db_connect.php');

// Retrieving all friends
$friend_query = "SELECT firstname, lastname, memid FROM Members m, Friends f WHERE m.email = f.friend_1 AND friend_2='$email'
			UNION
		  SELECT firstname, lastname, memid FROM Members m, Friends f WHERE m.email = f.friend_2 AND friend_1='$email';";
$friend_query_result = mysqli_query($conn, $friend_query);
$friend_result = mysqli_fetch_all($friend_query_result, MYSQLI_ASSOC);

// Retrieving all neighbors
$neighbor_query = "SELECT memid, firstname, lastname FROM Neighbor, Members WHERE memail='$email' AND email = neighbor;";
$neighbor_query_result = mysqli_query($conn, $neighbor_query);
$neighbor_result = mysqli_fetch_all($neighbor_query_result, MYSQLI_ASSOC);

// Retrieving belonged block
$block_query = "SELECT bid, hid, bname, hname, hcity, hstate FROM Members, Blocks, Hoods WHERE email='$email' AND mblock = bid AND bhood = hid;";
$block_query_result = mysqli_query($conn, $block_query);
$block_result = mysqli_fetch_all($block_query_result, MYSQLI_ASSOC);
foreach ($block_result as $block_info) {
	$block = $block_info["bid"];
	$bname = $block_info["bname"];
	$hood = $block_info["hid"];
	$hname = $block_info["hname"];
	$hcity = $block_info["hcity"];
	$hstate = $block_info["hstate"];
}

// Retrieving pending requested block
$requested_block_query = "SELECT bname, hname FROM Application, Blocks, Hoods WHERE applicant = '$email' AND app_status = 'pending' AND blockid = bid AND bhood = hid;";
$requested_block_query_result = mysqli_query($conn, $requested_block_query);
$requested_block_result = mysqli_fetch_all($requested_block_query_result, MYSQLI_ASSOC);

// Retrieving all pending friend request
$request_query = "SELECT memid, firstname, lastname, requester, date(request_time) AS rtime FROM Friend_Request, Members 
				  WHERE requestee ='$email' AND requester = email AND request_status = 'pending';";
$request_query_result = mysqli_query($conn, $request_query);
$request_result = mysqli_fetch_all($request_query_result, MYSQLI_ASSOC);

// Retrieving all pending join request
$join_query = "SELECT memid, firstname, lastname, requester FROM Join_Request, Members 
			   WHERE requestee ='$email' AND requester = email AND request_status = 'pending';";
$join_query_result = mysqli_query($conn, $join_query);
$join_result = mysqli_fetch_all($join_query_result, MYSQLI_ASSOC);

// Retrieving all readable message
$message_query = 
"(SELECT * FROM Messages WHERE poster = '$email') 
UNION
(SELECT * FROM Messages WHERE audiences='block' AND audience_id = (SELECT mblock FROM Members WHERE email='$email'))
UNION
(SELECT * FROM Messages WHERE audiences = 'hood' AND audience_id = (SELECT bhood FROM Blocks b, Members m WHERE b.bid = m.mblock AND m.email='$email'))
UNION
(SELECT * FROM Messages WHERE audiences = 'friends' AND poster IN (SELECT friend_2 AS f FROM Friends WHERE friend_1='$email' UNION SELECT friend_1 AS f FROM Friends WHERE friend_2='$email'))
UNION
(SELECT * FROM Messages WHERE audiences = 'neighbors' AND poster IN (SELECT memail FROM Neighbor WHERE neighbor='$email'))
UNION
(SELECT * FROM Messages WHERE audiences = 'person' AND audience_id = (SELECT memid FROM Members WHERE email = '$email'))
ORDER BY post_time desc limit 5;";
$message_query_result = mysqli_query($conn, $message_query);
$message_result = mysqli_fetch_all($message_query_result, MYSQLI_ASSOC);

// Clean up
mysqli_free_result($friend_query_result);
mysqli_free_result($block_query_result);
mysqli_free_result($neighbor_query_result);
mysqli_free_result($request_query_result);
mysqli_free_result($message_query_result);
mysqli_free_result($requested_block_query_result);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
	<title>HomePage</title>
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
		<a href="Profile.php" style="float: left;"><?php echo $fname." ".$lname; ?></a>
		<a href="Logout.php" style="float: right;">Log Out</a>
		<br><br>

		<div style="border-style: outset; border-radius: 8px">
			Most Recent 5 Messages:<br><br>
			<?php  
				foreach ($message_result as $message) {
					echo "<div style='border-style: outset; width:90%; margin:auto'>";
			?>
			<a href="MessagePage.php?from=homepage&id=<?php echo $message['mesid'] ?>"><?php echo $message["subjects"] ?></a>
			<?php
					echo "</div><br>";
				}
			?>
			<a href="ViewAllMessage.php">View More</a><br><br>
			<a href="SearchMessage.php">Search Messages</a><br><br>
			<a href="PostMessage.php">Post New Message</a><br><br>
		</div><br>

		<div style="width: 49%; float: left">
			<div style="border-style: outset; border-radius: 8px;">
				Friends:<br><br>
				<?php
					foreach ($friend_result as $friend) {
						echo "<div style='border-style: outset; width:50%; margin:auto'>";
				?>
						<a href="UserProfile.php?from=homepage&id=<?php echo $friend['memid']?>"><?php echo $friend["firstname"]." ".$friend["lastname"]; ?></a>
				<?php
						echo "</div><br>";
					}
					echo "<a href='FindFriend.php'>Add New Friends</a><br><br>";
				?>
			</div>
			<div style="border-style: outset; border-radius: 8px;">
				Friend Request:<br><br>
				<?php  
					foreach ($request_result as $request) {
						echo "<div style='border-style: outset; width:50%; margin:auto'>";
				?>
				<a href="UserProfile.php?from=homepage&id=<?php echo $request['memid']?>"><?php echo $request["firstname"]." ".$request["lastname"]; ?></a>
				<br>
				<a href="RespondRequest.php?to=<?php echo $request['requester']?>&respond=approved">approve</a>
				<br>
				<a href="RespondRequest.php?to=<?php echo $request['requester']?>&respond=denied">deny</a>
				<?php
						echo "</div><br>";
					}
				?>
			</div>
			<div style="border-style: outset; border-radius: 8px;">
				Join Request: <br><br>
				<?php  
					foreach ($join_result as $request) {
						echo "<div style='border-style: outset; width:50%; margin:auto'>";
				?>
				<a href="UserProfile.php?from=homepage&id=<?php echo $request['memid']?>"><?php echo $request["firstname"]." ".$request["lastname"]; ?></a>
				<br>
				<a href="RespondJoinRequest.php?to=<?php echo $request['requester']?>&respond=approved">approve</a>
				<br>
				<a href="RespondJoinRequest.php?to=<?php echo $request['requester']?>&respond=denied">deny</a>
				<?php
						echo "</div><br>";
					}
				?>
			</div>
		</div>

		<div style="width: 49%; float: right" >
			<div style="border-style: outset; border-radius: 8px;">
				Your Block:<br><br>
				<?php 
					if (!empty($requested_block_result)) {
						foreach ($requested_block_result as $requested_block) {
							echo "You request to join <br><br>";
							echo $requested_block["bname"].",<br>".$requested_block["hname"];
						}
					} elseif (empty($block)){
						echo "You are not a memebr of any block.<br><br>";
						echo "<a href='JoinBlock.php'>Join A Block</a>";
					} else{
						echo "$bname<br>$hname<br>$hcity<br>$hstate<br><br>";
						echo "<a href='LeaveBlock.php'>Leave Your Block</a>";
					}
				?><br><br>
			</div>
			<div style="border-style: outset; border-radius: 8px;">
				Neighbors:<br><br> 
				<?php  
					if (empty($block)){
						echo "Please join a block first.<br><br>";
					} else {
						foreach ($neighbor_result as $neighbor) {
							echo "<div style='border-style: outset; width:50%; margin:auto'>";
				?>
							<a href="UserProfile.php?from=homepage&id=<?php echo $neighbor['memid']?>"><?php echo $neighbor["firstname"]." ".$neighbor["lastname"]; ?></a>
				<?php
							echo "</div><br>";
						}
						echo "<a href='FindNeighbor.php'>Add New Neighbors</a><br><br>";
					}
				?>
			</div>
		</div>

		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>