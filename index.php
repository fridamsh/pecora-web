<?php
	include_once 'login.php';
?>

<section>
	<div>
		<?php
			if (isset($_SESSION['u_id'])) {
				$status = "You are logged in :D";
			} else {
				$status = "You are not logged in :(";
			}
		?>
		<p><?php echo $status ?></p>
	</div>
</section>