<?php
include_once '../includes/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$hikes = json_decode($_POST["hikes"]);

	for ($i = 0; $i < length($hikes); $i++) {
		$title = $hikes[0]->title;
		$name = $hikes[0]->name;	
		$participants = $hikes[0]->participants;	
		$weather = $hikes[0]->weather;	
		$description = $hikes[0]->description;	
		$startdate = $hikes[0]->startdate;	
		$enddate = $hikes[0]->enddate;	
		$mapfile = $hikes[0]->mapfile;	
		$distance = $hikes[0]->distance;	
		$observationPoints = $hikes[0]->observationPoints;	
		$track = $hikes[0]->track;	
		$userId = $hikes[0]->userId;	
		$localId = $hikes[0]->localId;

		$syncedHikes = 0;
		$alreadySyncedHikes = 0;
		$errors = 0;
		
		$sql = "SELECT * FROM hike WHERE userId='$userId' AND localId='$localId';";
		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) > 0) {
			$alreadySyncedHikes++;
			//$json['error'] = 'Turen er allerede synkronisert';
			//echo json_encode($json);
			//mysqli_close($conn);
		} else {
			$sql = "INSERT INTO hike (title, name, participants, weather, description, startdate, enddate, mapfile, distance, observationPoints, track, userId, localId) VALUES ('$title', '$name', '$participants', '$weather', '$description', '$startdate', '$enddate', '$mapfile', '$distance', '$observationPoints', '$track', '$userId', '$localId');";
			$result = mysqli_query($conn, $sql);
			
			if ($result == 1 ) {
				$syncedHikes++;
				//$json['success'] = 'Tur synkronisert';
			} else {
				$errors++;
				//$json['error'] = 'Det skjedde en feil ved synkronisering';
			}
			//echo json_encode($json);
			//mysqli_close($conn);
		}	
	}
	if ($errors > 0) {
		$json['error'] = $errors + ' feil, ' + $syncedHikes + ' synket og ' + $alreadySyncedHikes + ' allerede synket';
	} else {
		$json['success'] = $syncedHikes + ' synket, ' + $alreadySyncedHikes + ' allerede synket og 0 feil';
	}
	echo json_encode($json);
	mysqli_close($conn);
} else {
	// Echo json error
	$json['error'] = 'Det skjedde en feil ved synkronisering';
	echo json_encode($json);
	mysqli_close($conn);
}