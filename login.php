<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Pecora Web</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body style="background-color: #009688;">
	<div class="div-login">
		<!-- <?php
			if (isset($_SESSION['u_id'])) {
				echo '';
			} else {
				echo '';
			}
		?> -->
		<form>
			<input type="text" name="uid" placeholder="Username">
			<input type="password" name="pwd" placeholder="Password">
			<button type="submit" name="submit">Logg inn</button>
		</form>
	</div>
</body>
</html>