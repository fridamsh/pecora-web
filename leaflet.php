<?php
	include_once 'connection.php';
?>

<!DOCTYPE html>
<html>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.0/dist/leaflet.css"
   		integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
   		crossorigin=""/>
	<script src="https://unpkg.com/leaflet@1.3.0/dist/leaflet.js"
   		integrity="sha512-C7BBF9irt5R7hqbUm2uxtODlUVs+IsNu2UULGuZN7gM+k/mmeG4xvIEac01BtQa4YIkUpp23zZC4wIwuXaPMQA=="
   		crossorigin=""></script>
   	<script src='https://api.mapbox.com/mapbox-gl-js/v0.42.0/mapbox-gl.js'></script>
	<link href='https://api.mapbox.com/mapbox-gl-js/v0.42.0/mapbox-gl.css' rel='stylesheet' />
   	<style>
		body {background-color: white;}
		h1   {color: grey;}
		p    {color: black;}
		#mapid { position: absolute; top: 0; bottom: 0; left: 0; right: 0; width: 100%; }
	</style>
	<head>
		<title>Page Title</title>
	</head>
	<body>

		<?php
			$query = 'SELECT * FROM student';
			$result = mysqli_query($connect, $query);
			$number_of_rows = mysqli_num_rows($result);
	
			if($number_of_rows > 0) {
				while ($row = mysqli_fetch_assoc($result)) {
					$firstname = $row['firstname'];
					$lastname = $row['lastname'];
					$age = $row['age'];

					echo $firstname . ' ' . $lastname . ', age: ' . $age . '<br/>';
				}
			}
		?>

		<div id="mapid"></div>
		<script>
			var mymap = L.map('mapid').setView([63.416957, 10.402937], 13);
			L.tileLayer('http://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=topo2&zoom={z}&x={x}&y={y}', {
                attribution: '<a href="http://www.kartverket.no/">Kartverket</a>'
            }).addTo(mymap);
			var marker = L.marker([63.416957, 10.402937]).addTo(mymap);
			marker.bindPopup("<?php echo $firstname ?>").openPopup();
		</script>
		<!--<div id='map' style='width: 400px; height: 300px;'></div>
		<script>
			mapboxgl.accessToken = 'pk.eyJ1IjoiZnJpZGFzIiwiYSI6ImNqY2c2ODZmaTF4a2czMm8yczk5aTYyc3UifQ.P8DSNLT-TwsrwSGDURjHmA';
			var map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/streets-v10'
			});
		</script> -->
	</body>
</html>