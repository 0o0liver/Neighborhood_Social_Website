<?php  
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
} else {
	header('Location:LoginPage.php');
}

$email = $_SESSION["username"];
include("db_connect.php");

$query = "UPDATE Login_Info SET logout_time = current_timestamp WHERE memail = '$email' and year(logout_time) = 0;";
if (mysqli_query($conn, $query)){
	session_destroy();
	mysqli_close($conn);
	header('Location: LoginPage.php');

} else{
	echo mysqli_error($conn);
}
?>