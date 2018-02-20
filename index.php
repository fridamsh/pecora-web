<?php
	include_once 'login.php';
?>

<section>
	<div>
		<?php
			if (isset($_SESSION['u_id'])) {
				echo "You are logged in!";
			}
		?>
	</div>
</section>