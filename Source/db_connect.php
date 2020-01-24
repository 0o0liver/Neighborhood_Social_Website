<?php  

// Establish mysqli connection
$conn = mysqli_connect('localhost', 'Oliver', 'Oliver#1218', 'next_door');

// Check connection
if (!$conn){
	echo "Connection Error: " . mysqli_connect_error;
}

?>