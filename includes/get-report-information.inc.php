<?php

session_start();
include 'dbh.inc.php';
include '../models/Hike.php';

$userId = $_SESSION['u_id'];

$sql = "SELECT * FROM hike WHERE userId=$userId ORDER BY startdate ASC;";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
	$hikes = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$hike = new Hike();
		$hike->set_startdate($row['startdate']);
		$hike->set_enddate($row['enddate']);
		$hike->set_description($row['description']);
		$hike->set_observationPoints($row['observationPoints']);
		array_push($hikes, $hike);
	}	
	echo json_encode($hikes);
} else {
	$json['error']='error';
	echo json_encode($json);
}