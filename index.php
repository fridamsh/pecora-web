<?php
	session_start();
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
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

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
	var marker = L.marker([63.416957, 10.402937]).addTo(mymap);
	marker.bindPopup("Hello!").openPopup();

	var pointA = new L.LatLng(63.416898, 10.402840);
	var pointB = new L.LatLng(63.424502, 10.414083);
	var pointList = [pointA, pointB];

	var firstpolyline = new L.Polyline(pointList, {
	    color: 'blue',
	    weight: 3,
	    opacity: 0.8,
	    smoothFactor: 1
	});
	firstpolyline.addTo(mymap);
</script>
</html>