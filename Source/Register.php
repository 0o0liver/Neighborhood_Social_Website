<?php  

include('db_connect.php');

$errors = array('fname'=>'', 'lname'=>'', 'email'=>'', 'phone'=>'', 'address'=>'', 'city'=>'', 'state'=>'', 'password'=>'');

// Gathering user inputs 
if (isset($_REQUEST["submit"])) {
	// checking first name input
	if (empty($_REQUEST["fname"])){
		$errors['fname'] = 'First name is required! <br />';
	} else {
		$first_name = mysqli_real_escape_string($conn, $_REQUEST["fname"]);
	}

	// checking last name input
	if (empty($_REQUEST["lname"])) {
		$errors['lname'] = 'Last name is required! <br />';
	} else {
		$last_name = mysqli_real_escape_string($conn, $_REQUEST["lname"]);
	}

	// checking email input
	if (empty($_REQUEST["email"])) {
		$errors['email'] = 'Email is required! <br />';
	} else {
		$email = mysqli_real_escape_string($conn, $_REQUEST["email"]);
	}

	// checking phone input
	if (empty($_REQUEST["phone"])) {
		$errors['phone'] = 'Phone is required! <br />';
	} else {
		$phone = mysqli_real_escape_string($conn, $_REQUEST["phone"]);
	}

	// checking address input
	if (empty($_REQUEST["address"])) {
		$errors['address'] = 'Street address is required! <br />';
	} else {
		$street_address = mysqli_real_escape_string($conn, $_REQUEST["address"]);
	}

	// checking city input
	if (empty($_REQUEST["city"])) {
		$errors['city'] = 'City is required! <br />';
	} else {
		$city = mysqli_real_escape_string($conn, $_REQUEST["city"]);
	}

	// checking state input
	if (empty($_REQUEST["state"])) {
		$errors['state'] = 'State is required! <br />';
	} else {
		$state = mysqli_real_escape_string($conn, $_REQUEST["state"]);
	}

	// checking password input
	if (empty($_REQUEST["password"])) {
		$errors['password'] = 'Password is required! <br />';
	} else {
		$hashed_password = mysqli_real_escape_string($conn, password_hash($_REQUEST["password"], PASSWORD_DEFAULT));
	}

	if (array_filter($errors)){
		// errors are showing in divs
	} else {
		// Construct Query
		$query = "INSERT INTO Members(firstname, lastname, phone, email, passwords, street, city, state) VALUES
							 (\"$first_name\", \"$last_name\", \"$phone\", \"$email\", \"$hashed_password\", \"$street_address\", \"$city\", \"$state\")";

		// Get query result
		if (mysqli_query($conn, $query)){
		// success
		} else {
			echo mysqli_error($conn);
		}

		// Close connection
		mysqli_close($conn);

		// Change location
		header('Location: FinishRegistering.php');		
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
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
		<a href="LoginPage.php" style="float: left">Back</a>
		<h2>Required Personal Info</h2>
		<form method="POST" action="Register.php">
			<input type="text" name="fname" size="30" placeholder="First Name"><br>
			<div style="color:red"><?php echo $errors["fname"] ?></div><br>
			<input type="text" name="lname" size="30" placeholder="Last Name"><br>
			<div style="color:red"><?php echo $errors["lname"] ?></div><br>
			<input type="email" name="email" size="30" placeholder="Email"><br>
			<div style="color:red"><?php echo $errors["email"] ?></div><br>
			<input type="text" name="phone" size="30" placeholder="Phone Number"><br>
			<div style="color:red"><?php echo $errors["phone"] ?></div><br>
			<input type="text" name="address" size="30" placeholder="Street Address"><br>
			<div style="color:red"><?php echo $errors["address"] ?></div><br>
			<input type="text" name="city" size="30" placeholder="City"><br>
			<div style="color:red"><?php echo $errors["city"] ?></div><br>
			<input type="text" name="state" size="30" placeholder="State"><br>
			<div style="color:red"><?php echo $errors["state"] ?></div><br>
			<input type="text" name="password" size="30" placeholder="Password"><br>
			<div style="color:red"><?php echo $errors["password"] ?></div><br>
			<input type="submit" name="submit" value="Next"><br><br>
		</form>
	</div>
</body>
</html>