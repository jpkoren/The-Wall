<?php
	session_start();
	require_once('connection.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>The Wall</title>
        <link rel="stylesheet" href="style.css"/>
	</head>
    <body>
    	<div id="container">
    		<h1 id="indexTitle">The Wall</h1>
    		<h2>Registration</h2>

    		<form action="process.php" method="post">
				<input type="hidden" name="action" value="register">
    			<p>First Name: <input type="text" name="first_name"></p>
    			<p>Last Name: <input type="text" name="last_name"></p>
    			<p>Email: <input type="text" name="email"></p>
    			<p>Password: <input type="password" name="password"></p>
    			<p>Confirm Password: <input type="password" name="confirm_password"></p>
    			<p><input name="submit" type="submit" value="Submit"></p>
    		</form>

    		<div class="errors">
<?php
			if(isset($_SESSION['errors']))
			{
				foreach($_SESSION['errors'] as $error)
				{
					echo "<p>$error</p>";
				}

				unset($_SESSION['errors']);
			}			
?>
			</div>

			<div class="success">
<?php
				if(isset($_SESSION['first_name']) || isset($_SESSION['last_name']))
				{
?>					<p>Hello <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?>!  Thank you for registering!</p>
					<p>Please log in using the form below:</p>
<?php			}
?>
			</div>

			<h2>Log In</h2>

			<form action="process.php" method="post">
				<input type="hidden" name="action" value="login"/>
		        <p>Email: <input type="text" name="email"/></p>
		        <p>Password: <input type="password" name="password"/></p>
		        <p><input name="submit" type="submit" value="Submit"/></p>
			</form>

			<div class="errors">
<?php
			if(isset($_SESSION['login_errors']))
			{
				foreach($_SESSION['login_errors'] as $error)
				{
					echo "<p>$error</p>";
				}

				unset($_SESSION['login_errors']);
			}			
?>
			</div>
			
			<p><a href="reset.php">Reset Session</a></p>
		</div>
	</body>
</html>	