<?php

include 'dbh.inc.php';
include '../models/Hike.php';

$hikeId = $_POST['hikeId'];

$sql = "SELECT * FROM hike WHERE id=$hikeId;";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
	while ($row = mysqli_fetch_assoc($result)) {
		$hike = new Hike();
		$hike->set_id($row['id']);
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
	}
	echo json_encode($hike);
} else {
	echo json_encode('error');
}