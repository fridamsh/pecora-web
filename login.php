<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Pecora Web</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div class="div-login">
		<h2>Pecora</h2>
		<form class="login-form" action="includes/login.inc.php" method="POST">
			<input type="text" name="uid" placeholder="Username">
			<input type="password" name="pwd" placeholder="Password">
			<button type="submit" name="submit">Logg inn</button>
		</form>
	</div>
</body>
</html>