<?php 

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
} else {
	header('Location:LoginPage.php');
}
$email = $_SESSION["username"];

include('db_connect.php');

// Loading  page
$info = array("fname"=>"", "lname"=>"", "email"=>$email, "phone"=>"", "address"=>"", "city"=>"", "state"=>"", "description"=>"", "picture"=>"");
$errors = array('fname'=>'', 'lname'=>'', 'email'=>'', 'phone'=>'', 'address'=>'', 'city'=>'', 'state'=>'', 'profile'=>'');

$query = "SELECT * FROM Members WHERE email = '$email'";

$query_result = mysqli_query($conn, $query);

$result = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

mysqli_free_result($query_result);

foreach ($result as $record) {
	$info["fname"] = $record["firstname"];
	$info["lname"] = $record["lastname"];
	$info["phone"] = $record["phone"];
	$info["address"] = $record["street"];
	$info["city"] = $record["city"];
	$info["state"] = $record["state"];
	$info["picture"] = $record["profile_pic"];
	if (empty($record["descriptions"])){
		$info["description"] = "optional";
	} else {
		$info["description"] = $record["descriptions"];
	}
}

// After upload
if (isset($_REQUEST["file_up_load"])){
	// checking profile picture input
	$allowedExtension = array("jpeg", "png", "jpg");

	$target_dir = "img/";
	$filename = basename($_FILES["profile"]["name"]);
	$target_file = $target_dir . $filename;
	$fileTempName = $_FILES["profile"]["tmp_name"];
	$fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	//echo $filename;
	//echo $target_file;
	//echo $fileTempName;
	//echo $fileExtension;
	if (! in_array($fileExtension, $allowedExtension)){
		$errors['profile'] = 'File type not allowed! Use png, jpeg or jpg.';
	} else {
		move_uploaded_file($fileTempName, $target_file);
		$query = "UPDATE Members SET profile_pic = '$target_file' WHERE email = '$email';";
		if (mysqli_query($conn, $query)){
			// success
		} else {
			echo mysqli_error($conn);
		}
		mysqli_close($conn);
		header("Location: Profile.php");
	}
}

// After update
if (isset($_REQUEST["submit"])){

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

	// checking phone input
	if (empty($_REQUEST["phone"])) {
		$errors['phone'] = 'Phone is required!';
	} else {
		$phone = mysqli_real_escape_string($conn, $_REQUEST["phone"]);
	}

	// checking address input
	if (empty($_REQUEST["address"])) {
		$errors['address'] = 'Street address is required!';
	} else {
		$street_address = mysqli_real_escape_string($conn, $_REQUEST["address"]);
	}

	// checking city input
	if (empty($_REQUEST["city"])) {
		$errors['city'] = 'City is required!';
	} else {
		$city = mysqli_real_escape_string($conn, $_REQUEST["city"]);
	}

	// checking state input
	if (empty($_REQUEST["state"])) {
		$errors['state'] = 'State is required!';
	} else {
		$state = mysqli_real_escape_string($conn, $_REQUEST["state"]);
	}

	// checking description input
	$descriptions = mysqli_real_escape_string($conn, $_REQUEST["description"]);

	if (array_filter($errors)){
		// error showing in div
	} else {
		$query = "UPDATE Members SET
					firstname = '$first_name',
					lastname = '$last_name',
					phone = '$phone',
					street = '$street_address',
					city = '$city',
					state = '$state',
					descriptions = '$descriptions'
				  WHERE email = '$email'; ";
		if (mysqli_query($conn, $query)){
			// success
		} else {
			echo mysqli_error($conn);
		}
		mysqli_close($conn);
		header("Location: Profile.php");
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<style type="text/css">
		body, html{
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
		<h2>Profile</h2>
		<form method="POST" action="Profile.php" enctype="multipart/form-data">
			<img src="<?php echo $info["picture"] ?>" style="width: 100px; height: 100px; "><br><br>
			Update profile picture
			<input type="file" name="profile" id="profile"><br>
			<div style="color:red"><?php echo $errors["profile"] ?></div><br>
			<input type="submit" name="file_up_load" value="Upload">
		</form>
		<br>
		<form method="POST" action="Profile.php">
			First name:<br><input type="text" name="fname" value="<?php echo htmlspecialchars($info['fname']) ?>"><br>
			<div style="color:red"><?php echo $errors["fname"] ?></div><br>
			Last name:<br><input type="text" name="lname" value="<?php echo htmlspecialchars($info['lname']) ?>"><br>
			<div style="color:red"><?php echo $errors["lname"] ?></div><br>
			Email:<br><input type="email" name="email" readonly="true" value="<?php echo htmlspecialchars($info['email']) ?>"><br>
			<div style="color:red"><?php echo $errors["email"] ?></div><br>
			Phone:<br><input type="text" name="phone" value="<?php echo htmlspecialchars($info['phone']) ?>"><br>
			<div style="color:red"><?php echo $errors["phone"] ?></div><br>
			Address:<br><input type="text" name="address" value="<?php echo htmlspecialchars($info['address']) ?>"><br>
			<div style="color:red"><?php echo $errors["address"] ?></div><br>
			City:<br><input type="text" name="city" value="<?php echo htmlspecialchars($info['city']) ?>"><br>
			<div style="color:red"><?php echo $errors["city"] ?></div><br>
			State:<br><input type="text" name="state" value="<?php echo htmlspecialchars($info['state']) ?>"><br>
			<div style="color:red"><?php echo $errors["state"] ?></div><br>
			<script type="text/javascript">
				sessionStorage.setItem("address", "<?php echo $info["address"]; ?>");
				sessionStorage.setItem("city", "<?php echo $info["city"]; ?>");
				sessionStorage.setItem("state", "<?php echo $info["state"]; ?>");
			</script>
			<iframe src="map.html"></iframe><br><br>
			Description:<br><textarea rows="10" cols="100" name="description" ><?php echo htmlspecialchars($info['description']) ?></textarea><br><br>
			<input type="submit" name="submit" value="Update"><br><br>
		</form>
		<div style='clear:both;'></div>
	</div>
	<br><br><br><br><br><br>
</body>
</html>