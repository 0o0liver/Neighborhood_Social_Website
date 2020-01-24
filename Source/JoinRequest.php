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
$block = htmlspecialchars($_GET['block']);
echo $email;

// Gather member count from this block
$mcount_query = "SELECT COUNT(*) as mcount FROM Members WHERE mblock=$block;";
$mcount_query_result = mysqli_query($conn, $mcount_query);
$mcount_result = mysqli_fetch_all($mcount_query_result, MYSQLI_ASSOC);
foreach ($mcount_result as $result) {
	$mcount = $result["mcount"];
}

// Insert into Application tabale
$insert_query = "INSERT INTO Application(applicant, blockid, member_count) VALUES ('$email', $block, $mcount);";
if (mysqli_query($conn, $insert_query)){
	header("Location: HomePage.php");
} else {
	echo mysqli_error($conn);
}

// Cleaning up
mysqli_free_result($mcount_query_result);
mysqli_close($conn);



?>