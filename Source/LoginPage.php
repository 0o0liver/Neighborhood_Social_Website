<?php  

include('db_connect.php');

$errors = array('email'=>'', 'password'=>'', 'login'=>'');

if (isset($_REQUEST["submit"])){
	if (empty($_REQUEST["Email"])){
		$errors['email'] = "Please enter a email address.";
	} else {
		$email = mysqli_real_escape_string($conn, $_REQUEST["Email"]);
	}

	if (empty($_REQUEST["Password"])){
		$errors['password'] = "Please enter a password.";
	} else {
		$password = mysqli_real_escape_string($conn, $_REQUEST["Password"]);
	}

	if (array_filter($errors)){
		// errors are showing in divs
	} else{
		$query = "SELECT firstname, lastname, passwords FROM Members where email='$email'";
		$query_result = mysqli_query($conn, $query);
		$result = mysqli_fetch_all($query_result, MYSQLI_ASSOC);
		mysqli_free_result($query_result);
		foreach ($result as $row){
			$hashedPassword = $row['passwords'];
			$fname = $row['firstname'];
			$lname = $row['lastname'];
		}
		if (password_verify($password, $hashedPassword)){
			$query = "INSERT INTO Login_Info(memail) VALUES ('$email');";
			if (mysqli_query($conn, $query)){
				session_start();
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = $email;
				$_SESSION['fname'] = $fname;
				$_SESSION['lname'] = $lname;
				mysqli_close($conn);
				header('Location:HomePage.php');
			} else{
				echo mysqli_error($conn);
			}
		} else {
			$errors['login'] = 'Incorrect Email and/or Password.';
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
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
		<h1>Welcome</h1>
		<h2>Login</h2>
		<form method="POST" action="LoginPage.php">
			Email:
			<input type="email" name="Email" size="50"><br>
			<div style="color:red"><?php echo $errors['email'] ?></div><br>
			Password:
			<input type="password" name="Password" size="50"><br>
			<div style="color:red"><?php echo $errors['password'] ?></div><br>
			<input type="submit" name="submit" value="Login"><br><br>
			<div style="color:red"><?php echo $errors['login'] ?></div><br>
		</form>
		First time visiting our site? 
		<a href="Register.php">Register</a>.
		<br><br>
	</div>
</body>
</html>