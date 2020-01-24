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
$neighbor = htmlspecialchars($_GET['neighbor']);

// Add neighbor
$query = "INSERT INTO Neighbor(memail, neighbor) VALUES ('$email', '$neighbor');";
if (mysqli_query($conn, $query)){
	header("Location: FindNeighbor.php");
} else {
	echo mysqli_error($conn);
}

?>