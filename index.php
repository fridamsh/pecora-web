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

	                <a>Tur tirsdag morgen 24/02/2018 12:50</a>
	                <a>Tur søndag ettermiddag 27/02/2018 13:40</a>
	                
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

    function reqListener () {
      console.log(this.responseText);
    }

    var oReq = new XMLHttpRequest(); //New request object
    oReq.onload = function() {
    	var obj = JSON.parse(this.responseText);
    	if (obj == 'error') {
    		alert("Ingen turer å vise");
    	} else {
    		for (var k = 0; k < obj.length; k++) {
    			var title = obj[k].title;
		    	var name = obj[k].name;
		    	var participants = obj[k].participants;
		    	var weather = obj[k].weather;
		    	var description = obj[k].description;
		    	var startdate = obj[k].startdate;
		    	var dateStart = new Date(Number(startdate));
		    	var enddate = obj[k].enddate;
		    	var dateEnd = new Date(Number(enddate));
		    	var mapfile = obj[k].mapfile;
		    	var distance = obj[k].distance;
		    	var userId = obj[k].userId;
		    	var localId = obj[k].localId;
		    	var observationPoints = obj[k].observationPoints;
		    	var track = obj[k].track;

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

		    	//Decode track
		    	trackPointList = [];
		    	var jsonTrack = JSON.parse(track);
		    	for (var i = 0; i < jsonTrack.length; i++) {
		    		var latitude = Number(jsonTrack[i].mLatitude);
					var longitude = Number(jsonTrack[i].mLongitude);
					var point = new L.LatLng(latitude, longitude);
					trackPointList.push(point);
		    	}
		    	var marker = L.marker(trackPointList[0], {icon: startIcon}).addTo(mymap);
				marker.bindPopup("<b>Start</b> "+dateStart.format("HH:MM"));
				var marker = L.marker(trackPointList[trackPointList.length-1], {icon: stopIcon}).addTo(mymap);
				marker.bindPopup("<b>Slutt</b> "+dateEnd.format("HH:MM"));

		    	var trackPolyline = new L.Polyline(trackPointList, {
				    color: 'red',
				    weight: 4,
				    opacity: 0.8,
				    smoothFactor: 1
				}).bindPopup('<b>'+title+'</b><br>'+dateStart.format("dd/mm/yyyy HH:MM")+'-'+dateEnd.format('HH:MM')+'<br><b>Gjeter:</b> '+name+'<br><b>Deltakere:</b> '+participants+'<br><b>Vær:</b> '+weather+'<br><b>Detaljer:</b> '+description+'<br><b>Distanse:</b> '+distance+'<br><b>Antall sau sett:</b> '+3);

				trackPolyline.addTo(mymap);
				mymap.fitBounds(trackPolyline.getBounds());

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

				//Decode observation points
				var jsonObservationPoints = JSON.parse(observationPoints);
				//alert(jsonObservationPoints);
				for (var i = 0; i < jsonObservationPoints.length; i++) {
					var latitude = Number(jsonObservationPoints[i].locationPoint.mLatitude);
					var longitude = Number(jsonObservationPoints[i].locationPoint.mLongitude);
					var pointParent = new L.LatLng(latitude, longitude);
					var date = new Date(Number(jsonObservationPoints[i].timeOfObservationPoint));
					var marker = L.marker(pointParent, {icon: redIcon}).addTo(mymap);
					marker.bindPopup("<b>Observasjonspunkt "+jsonObservationPoints[i].pointId+"</b><br>Kl. "+date.format("HH:MM")+"<br>Sau sett: "+jsonObservationPoints[i].sheepCount);
					//Decode observations
					var observationList = jsonObservationPoints[i].observationList;
					for (var j = 0; j < observationList.length; j++) {
						var latitude = Number(observationList[j].locationObservation.mLatitude);
						var longitude = Number(observationList[j].locationObservation.mLongitude);
						var pointChild = new L.LatLng(latitude, longitude);
						var marker = L.marker(pointChild, {icon: blueIcon}).addTo(mymap);
						marker.bindPopup("<b>Observasjon "+observationList[j].observationId+"</b><br>Type: "+observationList[j].typeOfObservation+"<br>Antall: "+observationList[j].sheepCount);
						// Add polyline between observation point and observation
						var line = new L.Polyline([pointParent,pointChild], {
						    color: 'blue',
						    weight: 3,
						    opacity: 0.8,
						    smoothFactor: 1
						});
						line.addTo(mymap);
					}
				}
	    	}
    	}
    };
    oReq.open("get", "includes/get-data.inc.php", true);
    oReq.send();

</script>
</html>