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

// Updating table
$query = "UPDATE Members SET mblock = NULL WHERE email = '$email'";
if (mysqli_query($conn, $query)){
	header("Location: HomePage.php");
} else {
	echo mysqli_error($conn);
}

?>