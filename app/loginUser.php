<?php
include_once '../includes/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$uid = mysqli_real_escape_string($conn, $_POST['username']);
	$pwd = mysqli_real_escape_string($conn, $_POST['password']);
	//Error handlers
	//Check if inputs are empty
	if (empty($uid) || empty($pwd)) {
		// Echo json error
		$json['error'] = 'E1';
		echo json_encode($json);
		mysqli_close($conn);
	} else {
		$sql = "SELECT * FROM users WHERE user_uid='$uid' OR user_email='$uid';";
		$result = mysqli_query($conn, $sql);
		$resultCheck = mysqli_num_rows($result);
		if ($resultCheck < 1) {
			// Echo json error - user does not exist
			$json['error'] = 'E2';
			echo json_encode($json);
			mysqli_close($conn);
		} else {
			if ($row = mysqli_fetch_assoc($result)) {
				//De-hashing the password
				$hashedPwdChecked = password_verify($pwd, $row['user_pwd']);
				if ($hashedPwdChecked == false) {
					// Echo json error - wrong password
					$json['error'] = 'E3';
					echo json_encode($json);
					mysqli_close($conn);
				} elseif ($hashedPwdChecked == true) {
					//Log in the user here and echo the user data
					$json['success'] = 'success';
					$json['id'] = $row['user_id'];
					$json['first'] = $row['user_first'];
					$json['last'] = $row['user_last'];
					$json['email'] = $row['user_email'];
					$json['username'] = $row['user_uid'];
					echo json_encode($json);
					mysqli_close($conn);
				}
			}
		}
	}
} else {
	// Echo json error
	$json['error'] = 'E5';
	echo json_encode($json);
	mysqli_close($conn);
}