<?php
	session_start();
	include_once 'includes/dbh.inc.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pecora Web</title>

	<link rel="stylesheet" type="text/css" href="css/style.css">

	<!-- Leaflet css -->
	<link rel="stylesheet" type="text/css" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
	<!-- Leaflet javascript -->
	<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<!-- Custom fonts for this template -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<style>
		#mapwrap {
			width: 100%;
			height: 100%;
		}
		#map { 
			width: 100%;
			height: 100%;
		}
	</style>

</head>
<body>
	<?php
		if (isset($_SESSION['u_id'])) {
			$status = "Innlogget som: ";
		} else {
			header("Location: login.php");
			exit();
		}
	?>

	<?php
		$sql = "SELECT * FROM hike WHERE localId=4;";
		$result = mysqli_query($conn, $sql);
		$resultCheck = mysqli_num_rows($result);

		if ($resultCheck > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$title = $row['title'];
				$observationPoints = $row['observationPoints'];
				$track = $row['track'];
			}
		}

		$json_obs_point = json_decode($observationPoints);
		// Må hente ut json-objektet som ligger på posisjon 0 i arrayet
		//echo $json_obs_point[0]->timeOfObservationPoint . "<br><br>";
		
		$json_track = json_decode($track);
		
		$track_points = array();
		foreach ($json_track as $key => $value) {
			array_push($track_points, array($value->mLatitude, $value->mLongitude));
		}

	?>

	<div id="mapwrap">
		<div id="map"></div>
		<div class="leaflet-control-main-menu-button" title="Menu"></div>
		<div class="leaflet-top leaflet-right">
			<form action="includes/logout.inc.php" method="POST">
				<button type="submit" name="submit" class="leaflet-control btn btn-primary">Logg ut</button>
			</form>
		</div>
	</div>
</body>
<script>
	var mymap = L.map('map').setView([63.416957, 10.402937], 13);
	L.tileLayer('http://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=topo2&zoom={z}&x={x}&y={y}', {
        attribution: '<a href="http://www.kartverket.no/">Kartverket</a>'
    }).addTo(mymap);

	// Initialize point list
	var pointList = [];
	// Get track points from php-variable
	var jArray= <?php echo json_encode($track_points); ?>;
	// Go through each point and add latitude and longitude as a point to the list
	for (var i = 0; i < jArray.length; i++) {
		var res = jArray[i].toString().split(',');
		var latitude = Number(res[0]);
		var longitude = Number(res[1]);
		var point = new L.LatLng(latitude, longitude);
		pointList.push(point);
    }
    // Make polyline with the point list
	var trackPolyline = new L.Polyline(pointList, {
	    color: 'red',
	    weight: 4,
	    opacity: 0.8,
	    smoothFactor: 1
	}).bindPopup('<?= $title ?>');
	/*trackPolyline.on('click', function(e) {
		var marker = L.marker([63.416957, 10.402937]).addTo(mymap);
		marker.bindPopup("Tur: <?= $title ?>").openPopup();
	});*/
	// Add the polyline to the map
	trackPolyline.addTo(mymap);
	mymap.fitBounds(trackPolyline.getBounds());
</script>
</html>