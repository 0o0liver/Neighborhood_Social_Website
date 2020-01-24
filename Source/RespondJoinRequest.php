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
$requester = htmlspecialchars($_GET['to']);
$respond = htmlspecialchars($_GET['respond']);

// Respond request
$query = "UPDATE Join_Request SET request_status = '$respond', responed_time=CURRENT_TIMESTAMP WHERE requester = '$requester' AND requestee='$email';";
if (mysqli_query($conn, $query)){
	header("Location: HomePage.php");
} else{
	echo mysqli_error($conn);
}


?>