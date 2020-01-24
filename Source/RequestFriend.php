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
$requestee = htmlspecialchars($_GET['requestee']);

// Add request
$query = "INSERT INTO Friend_Request(requester, requestee) VALUES ('$email', '$requestee');";
if (mysqli_query($conn, $query)){
	header("Location: FindFriend.php");
} else {
	echo mysqli_error($conn);
}
?>