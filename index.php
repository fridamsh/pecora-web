<?php
	session_start();
	include_once 'includes/dbh.inc.php';
	include 'models/Hike.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
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
    <!--<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">-->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Sidebar -->
    <link rel="stylesheet" href="css/leaflet-sidebar.css" />
    <script src="js/leaflet-sidebar.js"></script>

    <!-- Date format -->
    <script src="js/Date.js"></script>
    <!--<script src="js/getMostRecentHike.js"></script>-->

	<style>
		body {
            padding: 0;
            margin: 0;
        }
		html, body, #map {
            height: 100%;
            font: 10pt "Helvetica Neue", Arial, Helvetica, sans-serif;
        }
        p {
        	color: #000;
        	line-height: 30px;
        	font-size: 11pt;
        }
		#mapwrap {
			width: 100%;
			height: 100%;
		}
        .sidebar-tabs button:hover {
        	background-color: #eee;
        }
        .logout-div button {
        	background-color: #009688;
        	border-color: #00897B;
        }
        .logout-div button:hover {
        	background-color: #26A69A;
        	border-color: #00897B;
        }
	</style>

</head>
<body>
	<?php
		if (isset($_SESSION['u_id'])) {
			$userId = $_SESSION['u_id'];
			$userFirst = $_SESSION['u_first'];
			$userLast = $_SESSION['u_last'];
			$userEmail = $_SESSION['u_email'];
			$userUid = $_SESSION['u_uid'];
		} else {
			header("Location: login.php");
			exit();
		}
	?>

	<?php
		$sql = "SELECT * FROM hike WHERE userId=$userId AND startdate=(SELECT MAX(startdate) FROM hike WHERE userId=$userId);";
		$result = mysqli_query($conn, $sql);
		$resultCheck = mysqli_num_rows($result);
		// Initialize variables in case there doesn't exist any hikes
		$title = $name = $participants = $weather = $description = $distance = $startdate = $starttime = $endtime = $sheepCount = "";
		$track_points = array();
		$observation_points = array();
		// Check if there exists a hike
		if ($resultCheck > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$title = $row['title'];
				$name = $row['name'];
				$participants = $row['participants'];
				$weather = $row['weather'];
				$description = $row['description'];
				$startdate = $row['startdate'];
				$endtime = $row['enddate'];
				$distance = $row['distance'];
				$observationPoints = $row['observationPoints'];
				$track = $row['track'];
			}
			// Convert unix time to human readable date
			$seconds = $startdate / 1000;
			date_default_timezone_set('Europe/Oslo');
			$startdate = date("d/m/Y", $seconds);
			$starttime = date("H:i", $seconds);
			$secondsEnd = $endtime / 1000;
			$endtime = date("H:i", $secondsEnd);
			
			// Decode json objects
			// Get relevant information from observationPoints
			//$observation_list = array();
			$json_obs_point = json_decode($observationPoints);
			foreach ($json_obs_point as $key => $value) {
				$obsLength = sizeof($value->observationList);
				$observationPoint = array();
				array_push($observationPoint, $value->locationPoint->mLatitude, $value->locationPoint->mLongitude, $value->pointId, $value->sheepCount, $value->timeOfObservationPoint);
				// Loop through observationList and add to observationPoint
				for ($i=0; $i < $obsLength; $i++) { 
					array_push($observationPoint, $value->observationList[$i]->details, $value->observationList[$i]->locationObservation->mLatitude, $value->observationList[$i]->locationObservation->mLongitude, $value->observationList[$i]->observationId, $value->observationList[$i]->sheepCount, $value->observationList[$i]->typeOfObservation);
				}
				array_push($observation_points, $observationPoint);
			}
			// Get relevant information from track
			$json_track = json_decode($track);
			foreach ($json_track as $key => $value) {
				array_push($track_points, array($value->mLatitude, $value->mLongitude));
			}
		}
		$sql2 = "SELECT * FROM hike WHERE userId=$userId;";
		$result2 = mysqli_query($conn, $sql2);
		$resultCheck2 = mysqli_num_rows($result2);
		$hikes = array();
		if ($resultCheck2 > 0) {
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
				$hike->set_observationPoints($row['observationPoints']);
				$hike->set_track($row['track']);
				$hike->set_userId($row['userId']);
				$hike->set_localId($row['localId']);
				array_push($hikes, $hike);
			}
		}
	?>

	<div id="mapwrap">
		<div id="sidebar" class="sidebar collapsed">
	        <!-- Nav tabs -->
	        <div class="sidebar-tabs">
	            <ul role="tablist">
	                <li><a href="#home" role="tab"><i class="fa fa-bars"></i></a></li>
	                <li><a href="#profile" role="tab"><i class="fa fa-user"></i></a></li>
	                <li><a href="#dates" role="tab"><i class="fa fa-calendar"></i></a></li>
	                <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
	            </ul>

	            <!--<ul role="tablist">
	                <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
	            </ul>-->
	        </div>

	        <!-- Tab panes -->
	        <div class="sidebar-content">
	            <div class="sidebar-pane" id="home">
	                <h1 class="sidebar-header">
	                    Pecora
	                    <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
	                </h1>

	                <p>Hva skal man ha her?</p>
	            </div>

	            <div class="sidebar-pane" id="profile">
	                <h1 class="sidebar-header">Profil<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>

	                <p>Navn: <?= $userFirst, " ", $userLast ?></p>
	                <p>E-mail: <?= $userEmail ?></p>
	                <p>Brukernavn: <?= $userUid ?></p>
	                <div class="logout-div">
						<form action="includes/logout.inc.php" method="POST">
							<button type="submit" name="submit" class="btn btn-primary">Logg ut</button>
						</form>
					</div>
	            </div>

	            <div class="sidebar-pane" id="dates">
	                <h1 class="sidebar-header">Datoer<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>

	                <a><?= $hikes[1]->get_title(), " ", date("d/m/Y H:i", $hikes[1]->get_startdate()/1000), "<br>" ?></a>
	                <a><?= $hikes[0]->get_title(), " ", date("d/m/Y H:i", $hikes[0]->get_startdate()/1000) ?></a>
	                
	            </div>

	            <div class="sidebar-pane" id="settings">
	                <h1 class="sidebar-header">Innstillinger<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>

	                <p>Trenger jeg innstillinger?</p>
	            </div>
	        </div>
	    </div>

		<div id="map" class="sidebar-map"></div>
		

	</div>
</body>
<script>
	var mymap = L.map('map').setView([63.416957, 10.402937], 13);
	L.tileLayer('http://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=topo2&zoom={z}&x={x}&y={y}', {
	    attribution: '<a href="http://www.kartverket.no/">Kartverket</a> | <a href="http://www.ingridogsondre.no" target="_blank">Frida</a>'
	}).addTo(mymap);

	var sidebar = L.control.sidebar('sidebar').addTo(mymap);

	var pointList = [];
	var jArray = <?php echo json_encode($track_points); ?>;
	// Check if track points is empty or not
	if (jArray.length > 0) {
		var observationPointList = [];
		var antallSau = 0;
		var jArray2 = <?php echo json_encode($observation_points); ?>;
		if (jArray2.length > 0) {
			for (var i = 0; i < jArray2.length; i++) {
				var res = jArray2[i].toString().split(',');
				//alert(res);
				pointListObsPoint = [];
				antallSau += Number(res[3]);

				var redIcon = L.icon({
				    iconUrl: 'img/marker-icon-2x-red-2.png',
				    iconSize: [25, 41],
				    iconAnchor: [13, 40],
				    popupAnchor: [0, -33],
				    shadowUrl: 'img/marker-shadow.png',
				    shadowSize: [41, 41],
				    shadowAnchor: [13, 40]
				});
				var blueIcon = L.icon({
				    iconUrl: 'img/marker-icon-2x-blue.png',
				    iconSize: [25, 41],
				    iconAnchor: [13, 40],
				    popupAnchor: [0, -33],
				    shadowUrl: 'img/marker-shadow.png',
				    shadowSize: [41, 41],
				    shadowAnchor: [13, 40]
				});

				// Observation point
				var latitude = Number(res[0]);
				var longitude = Number(res[1]);
				var point = new L.LatLng(latitude, longitude);
				pointListObsPoint.push(point);
				var date = new Date(Number(res[4]));
				// Add marker
				var marker = L.marker(point, {icon: redIcon}).addTo(mymap);
				marker.bindPopup("<b>Observasjonspunkt "+res[2]+"</b><br>Kl. "+date.format("HH:MM")+"<br>Sau sett: "+res[3]);

				// Observation
				var latitude = Number(res[6]);
				var longitude = Number(res[7]);
				var point = new L.LatLng(latitude, longitude);
				pointListObsPoint.push(point);
				var date = new Date(Number(res[4]));
				// Add marker
				var type = res[10];
				var marker = L.marker(point, {icon: blueIcon}).addTo(mymap);
				marker.bindPopup("<b>Observasjon "+res[8]+"</b><br>Type: "+type+"<br>Antall: "+res[9]);

				// Add polyline between observation point and observation
				var trackPolyline = new L.Polyline(pointListObsPoint, {
				    color: 'blue',
				    weight: 3,
				    opacity: 0.8,
				    smoothFactor: 1
				});
				trackPolyline.addTo(mymap);
				// Check if there are more observations from the observation point
				if (res.length > 11) {
					// Observation
					var latitude = Number(res[12]);
					var longitude = Number(res[13]);
					var point = new L.LatLng(latitude, longitude);
					var list = [];
					list.push(new L.LatLng(Number(res[0]), Number(res[1])));
					list.push(point);
					// Add marker
					var type = res[16];
					var marker = L.marker(point, {icon: blueIcon}).addTo(mymap);
					marker.bindPopup("<b>Observasjon "+res[14]+"</b><br>Type: "+type+"<br>Antall: "+res[15]);

					// Add polyline between observation point and observation
					var line = new L.Polyline(list, {
					    color: 'blue',
					    weight: 3,
					    opacity: 0.8,
					    smoothFactor: 1
					});
					line.addTo(mymap);
				} else if (res.length > 17) {
					// Observation
					var latitude = Number(res[18]);
					var longitude = Number(res[19]);
					var point = new L.LatLng(latitude, longitude);
					var list = [];
					list.push(new L.LatLng(Number(res[0]), Number(res[1])));
					list.push(point);
					// Add marker
					var type = res[22];
					var marker = L.marker(point, {icon: blueIcon}).addTo(mymap);
					marker.bindPopup("<b>Observasjon "+res[20]+"</b><br>Type: "+type+"<br>Antall: "+res[21]);

					// Add polyline between observation point and observation
					var line = new L.Polyline(list, {
					    color: 'blue',
					    weight: 3,
					    opacity: 0.8,
					    smoothFactor: 1
					});
					line.addTo(mymap);
				}
			}
		}
		for (var i = 0; i < jArray.length; i++) {
			var res = jArray[i].toString().split(',');
			var latitude = Number(res[0]);
			var longitude = Number(res[1]);
			var point = new L.LatLng(latitude, longitude);
			pointList.push(point);
		}
		var startIcon = L.icon({
		    iconUrl: 'img/marker-icon-start.png',
		    iconSize: [20, 20],
		    iconAnchor: [10, 10],
		    popupAnchor: [0, -5],
		    shadowUrl: 'img/shadow.png',
		    shadowSize: [20, 20],
		    shadowAnchor: [7, 11]
		});
		var stopIcon = L.icon({
		    iconUrl: 'img/marker-icon-stop.png',
		    iconSize: [20, 20],
		    iconAnchor: [10, 10],
		    popupAnchor: [0, -5],
		    shadowUrl: 'img/shadow.png',
		    shadowSize: [20, 20],
		    shadowAnchor: [7, 11]
		});
		var marker = L.marker(pointList[0], {icon: startIcon}).addTo(mymap);
		marker.bindPopup("<b>Start</b> <?= $starttime ?>");
		var marker = L.marker(pointList[pointList.length-1], {icon: stopIcon}).addTo(mymap);
		marker.bindPopup("<b>Slutt</b> <?= $endtime ?>");

		var trackPolyline = new L.Polyline(pointList, {
		    color: 'red',
		    weight: 4,
		    opacity: 0.8,
		    smoothFactor: 1
		}).bindPopup('<b><?= $title ?></b><br><?= $startdate ?> kl. <?= $starttime ?>-<?= $endtime ?><br><b>Gjeter:</b> <?= $name ?><br><b>Deltakere:</b> <?= $participants?><br><b>Vær:</b> <?= $weather ?><br><b>Detaljer:</b> <?= $description ?><br><b>Distanse:</b> <?= $distance ?><br><b>Antall sau sett:</b> '+antallSau);

		trackPolyline.addTo(mymap);
		mymap.fitBounds(trackPolyline.getBounds());
	}
</script>
</html>