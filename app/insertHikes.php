<?php
include_once '../includes/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$hikes = json_decode($_POST["hikes"]);

	$syncedHikes = 0;
	$alreadySyncedHikes = 0;
	$errors = 0;

	for ($i = 0; $i < sizeof($hikes); $i++) {
		$title = $hikes[$i]->title;
		$name = $hikes[$i]->name;
		$participants = $hikes[$i]->numberOfParticipants;	
		$weather = $hikes[$i]->weatherState;	
		$description = $hikes[$i]->description;	
		$startdate = $hikes[$i]->dateStart;	
		$enddate = $hikes[$i]->dateEnd;	
		$mapfile = $hikes[$i]->mapFileName;	
		$distance = $hikes[$i]->distance;	
		$observationPoints = json_encode($hikes[$i]->observationPoints, JSON_UNESCAPED_UNICODE);	
		$track = json_encode($hikes[$i]->trackPoints);	
		$userId = $hikes[$i]->userId;
		$localId = $hikes[$i]->id;
		
		$sql = "SELECT * FROM hike WHERE userId='$userId' AND localId='$localId';";
		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) > 0) {
			$alreadySyncedHikes++;
		} else {
			$sql = "INSERT INTO hike (title, name, participants, weather, description, startdate, enddate, mapfile, distance, observationPoints, track, userId, localId) VALUES ('$title', '$name', '$participants', '$weather', '$description', '$startdate', '$enddate', '$mapfile', '$distance', '$observationPoints', '$track', '$userId', '$localId');";
			$result = mysqli_query($conn, $sql);
			
			if ($result == 1 ) {
				$syncedHikes++;
			} else {
				$errors++;
			}
		}
	}
	if ($errors > 0) {
		$json['error'] = $errors . ' feil, ' . $syncedHikes . ' synket og ' . $alreadySyncedHikes . ' allerede synket';
	} else {
		$json['success'] = $syncedHikes . ' synket, ' . $alreadySyncedHikes . ' allerede synket og ' . $errors . ' feil';
	}
	echo json_encode($json);
	mysqli_close($conn);
} else {
	// Echo json error
	$json['error'] = 'Det skjedde en feil ved synkronisering';
	echo json_encode($json);
	mysqli_close($conn);
}