<?php
	session_start();
	include_once 'includes/dbh.inc.php';
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
		.lorem {
            font-style: italic;
            color: #AAA;
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
		$title = "";
		$track_points = array();
		if ($resultCheck > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$title = $row['title'];
				$observationPoints = $row['observationPoints'];
				$track = $row['track'];
			}
			$json_obs_point = json_decode($observationPoints);
			$json_track = json_decode($track);
			
			foreach ($json_track as $key => $value) {
				array_push($track_points, array($value->mLatitude, $value->mLongitude));
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
	            </ul>

	            <ul role="tablist">
	                <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
	            </ul>
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
	    attribution: '<a href="http://www.kartverket.no/">Kartverket</a>'
	}).addTo(mymap);

	var sidebar = L.control.sidebar('sidebar').addTo(mymap);

	/*var marker = L.marker([63.416957, 10.402937]).addTo(mymap);
	marker.bindPopup("Tur: <?= $title ?>").openPopup();*/

	var pointList = [];
	var jArray= <?php echo json_encode($track_points); ?>;
	// Check if track points is empty or not
	if (jArray.length > 0) {
		for (var i = 0; i < jArray.length; i++) {
			var res = jArray[i].toString().split(',');
			var latitude = Number(res[0]);
			var longitude = Number(res[1]);
			var point = new L.LatLng(latitude, longitude);
			pointList.push(point);
		}

		var trackPolyline = new L.Polyline(pointList, {
		    color: 'red',
		    weight: 4,
		    opacity: 0.8,
		    smoothFactor: 1
		}).bindPopup('<?= $title ?>');

		trackPolyline.addTo(mymap);
		mymap.fitBounds(trackPolyline.getBounds());
	}
	
</script>
</html>