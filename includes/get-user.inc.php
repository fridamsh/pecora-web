<?php

session_start();
include 'dbh.inc.php';

$userId = $_SESSION['u_id'];

$sql = "SELECT * FROM users WHERE user_id=$userId;";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck == 1) {
	$row = mysqli_fetch_assoc($result);
	$json['name'] = $row['user_first'];
	$json['lastname'] = $row['user_last'];
	$json['email'] = $row['user_email'];
	$json['username'] = $row['user_uid'];
	echo json_encode($json);
} else {
	echo json_encode('error');
}