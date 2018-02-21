<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>
	<title>Pecora Web</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<style>
	#map { 
		position: absolute;
		bottom: 0;
		width: 100%;
		height: 100%;
	}
</style>
<body>
	<div id="header">
		<!--<?php
			if (isset($_SESSION['u_id'])) {
				$status = "Innlogget som: ";
			} else {
				header("Location: login.php");
				exit();
			}
		?>
		<div id="second">
			<form action="includes/logout.inc.php" method="POST">
				<button type="submit" name="submit">Logg ut</button>
			</form>
		</div>
		<div id="first">
			<p>Innlogget som: <?php echo $_SESSION['u_uid'] ?></p>
		</div>
		<h2 style="margin-left: 275px;">Pecora</h2>-->
		<div id="wrapper">
		    <div id="left">
		        <div id="leftIn"><p style="color: transparent;">Hei</p></div>
		    </div>
		    <div id="middle">
		        <div id="middleIn">
		        	<h2>Pecora</h2>
		        </div>
		    </div>
		    <div id="right">
		        <div id="rightIn">
		        	<form action="includes/logout.inc.php" method="POST">
						<button type="submit" name="submit">Logg ut</button>
					</form>
					<p>Innlogget som: <?php echo $_SESSION['u_uid'] ?></p>
		        </div>
		    </div>
		</div>
	</div>
	<div id="content">
		<div id="map"></div>
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
	</div>
	<div id="footer">
		<p style="text-align: center; font-size: 12px;">&copy; Frida Schmidt-Hanssen 2018</p>
	</div>
</body>
</html>