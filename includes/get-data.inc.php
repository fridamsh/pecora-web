<?php

session_start();
include 'dbh.inc.php';
include '../models/Hike.php';

$userId = $_SESSION['u_id'];

$sql = "SELECT * FROM hike WHERE userId=$userId ORDER BY startdate DESC;";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
	$hikes = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$hike = new Hike();
		$hike->set_id($row['id']);
		$hike->set_title($row['title']);
		$hike->set_startdate($row['startdate']);
		array_push($hikes, $hike);
	}
	echo json_encode($hikes);
} else {
	echo json_encode('error');
}