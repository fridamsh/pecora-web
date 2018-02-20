<?php
	include_once 'login.php';
?>

<section>
	<div>
		<h2>Pecora</h2>
		<?php
			if (isset($_SESSION['u_id'])) {
				echo "You are logged in!";
			}
		?>
	</div>
</section>