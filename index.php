<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Pecora Web</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<section>
		<div>
			<?php
				if (isset($_SESSION['u_id'])) {
					$status = "Du er innlogget!";
				} else {
					header("Location: login.php");
					exit();
				}
			?>
			<p><?php echo $status ?></p>
			<form class="login-form" action="includes/logout.inc.php" method="POST">
				<button type="submit" name="submit">Logg ut</button>
			</form>
		</div>
	</section>
</body>
</html>