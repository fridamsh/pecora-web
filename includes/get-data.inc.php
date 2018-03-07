<?php

session_start();
include 'dbh.inc.php';
include '../models/Hike.php';

$userId = $_SESSION['u_id'];

$sql2 = "SELECT * FROM hike WHERE userId=$userId;";
$result2 = mysqli_query($conn, $sql2);
$resultCheck2 = mysqli_num_rows($result2);

if ($resultCheck2 > 0) {
	$hikes = array();
	while ($row = mysqli_fetch_assoc($result2)) {
		$hike = new Hike();
		$hike->set_title($row['title']);
		$hike->set_name($row['name']);
		$hike->set_participants($row['participants']);
		$hike->set_weather($row['weather']);
		$hike->set_description($row['description']);
		$hike->set_startdate($row['startdate']);
		$hike->set_enddate($row['enddate']);
		$hike->set_mapfile($row['mapfile']);
		$hike->set_distance($row['distance']);
		$hike->set_observationPoints($row['observationPoints']);
		$hike->set_track($row['track']);
		$hike->set_userId($row['userId']);
		$hike->set_localId($row['localId']);
		array_push($hikes, $hike);
	}
	echo json_encode($hikes);
} else {
	echo json_encode('error');
}