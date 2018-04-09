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
	<link rel="icon" type="image/png" href="img/ic_pecora_3.png">

	<link rel="stylesheet" type="text/css" href="css/style.css">

	<!-- Leaflet css -->
	<link rel="stylesheet" type="text/css" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
	<!-- Leaflet javascript -->
	<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>

	<!-- Latest compiled and minified Boostrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- moment.js -->
	<script src="moment/js/moment.js"></script>
	<script src="moment/locale/nb.js"></script>

	<!-- Latest compiled Bootstrap JavaScript -->
	<script src="bootstrap/dist/bootstrap.min.js"></script>
	<script src="bootstrap/js/transition.js"></script>
	<script src="bootstrap/js/collapse.js"></script>

	<!-- Boostrap DatetimePicker JS og CSS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"></link>

	<!-- Font awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Sidebar -->
    <link rel="stylesheet" href="css/leaflet-sidebar.css" />
    <script src="js/leaflet-sidebar.js"></script>

    <!-- Date format -->
    <script src="js/Date.js"></script>

    <!-- Geo location -->
	<link rel="stylesheet" href="css/L.Control.Locate.min.css" />
	<script src="js/L.Control.Locate.min.js" charset="utf-8"></script>

	<!-- jsPDF -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js" integrity="sha384-CchuzHs077vGtfhGYl9Qtc7Vx64rXBXdIAZIPbItbNyWIRTdG0oYAqki3Ry13Yzu" crossorigin="anonymous"></script>

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
        	color: #333;
        	line-height: 30px;
        	font-size: 11pt;
        }
        h2 {
        	font-size: 18pt;
        }
        h3 {
        	font-size: 13pt;
        }
        #home p, li {
        	color: #000;
        	line-height: 25px;
        	font-size: 11pt;
        }
        #home ul {
        	margin-left: 20px;
        }
        #settings ul {
        	margin-left: 20px;
        }
        #dates li {
        	line-height: 30px;
        	font-size: 11pt;
        	list-style: none;
        }
        #dates input[type="checkbox"] {
        	vertical-align: middle;
        	position: relative;
        	margin-bottom: 5px;
        }
        #dates a {
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
        .logout-div button:focus {
        	background-color: #009688;
        	border-color: #00897B;
        }
        #profile-div {
        	margin: auto;
        	text-align: center;
        }
        .circle {
        	margin: auto;
        	vertical-align: middle;
			border-radius: 50%;
			width: 100px;
			height: 100px;
		}
		#profile-circle {
			margin-top: 15px;
			margin-bottom: 10px;
			background: #ccc;
			vertical-align: middle;
			padding: 20px 0;
		}
	</style>
</head>
<body>
	<?php
		if (isset($_SESSION['u_id'])) {
			$userId = $_SESSION['u_id'];
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
	                <li><a id="dateLink" href="#dates" role="tab"><i class="fa fa-calendar"></i></a></li>
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

	                <h2>Velkommen til Pecora Web!</h2>
	                <p>Her kan du som bruker av Pecora-appen se dine synkroniserte turer med all tilbehørende informasjon.
	                	<br>
	                	<br>Det som er annerledes med Pecora web er at du kan velge å se alle turene dine samtidig på kartet eller noen få utvalgte. Kanskje du vil kunne se et mønster over hvor sauene dine befinner seg i området eller hvor rovdyrene pleier å være?
	                	<br>
	                	<br>I Pecora Web kan du gjøre følgende:</p>
	                <ul>
	                	<li>Se din profil</li>
	                	<li>Se alle dine synkroniserte turer</li>
	                	<li><i>Kommer flere funksjoner</i></li>
	                </ul>
	            </div>

	            <div class="sidebar-pane" id="profile">
	                <h1 class="sidebar-header">Profil<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>
	                <div id="profile-div">
	                	<div class="circle" id="profile-circle">
	                		<i class="fa fa-user" style="font-size: 60px; color: #777; vertical-align: middle;"></i>
	                	</div>
	                	<div id="profile-data"></div>
		                <div class="logout-div" style="margin-top: 15px;">
							<form action="includes/logout.inc.php" method="POST">
								<button type="submit" name="submit" class="btn btn-primary">Logg ut</button>
							</form>
						</div>
	                </div>
	            </div>

	            <div class="sidebar-pane" id="dates">
	                <h1 class="sidebar-header">Datoer<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>
	                <h3 style="margin-bottom: 10px;">Velg turene du vil se på kartet:</h3>

	                <span style="white-space:nowrap">
					    <label for="id1">Fra</label>
					    <div class="form-group">
				            <div class='input-group date' id='datetimepicker1'>
				                <input type='text' class="form-control" />
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
				            </div>
				        </div>
					</span>
					<span style="white-space:nowrap">
					    <label for="id1">Til</label>
					    <div class="form-group">
				            <div class='input-group date' id='datetimepicker2'>
				                <input type='text' class="form-control" />
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
				            </div>
				        </div>
					</span>
					<span style="white-space:nowrap" class="logout-div">
					    <button id="intervalBtn" type="submit" class="btn btn-primary">Se turer</button>
					    <button id="resetBtn" type="submit" class="btn btn-primary">Tilbakestill</button>
					</span>

					<ul id="hikes-interval-list" style="margin-top: 10px;"></ul>

	                <!--<ul id="hikes-list"></ul>-->
	                

	            </div>

	            <div class="sidebar-pane" id="settings">
	                <h1 class="sidebar-header">Innstillinger<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>

	                <h3 style="margin-bottom: 10px;">Rapport</h3>
	                <p>Her kan du laste ned en rapport over turene du har gått. 
	                	<br>Rapporten inneholder en liste av turer med informasjon som dato og klokkeslett, samt antall sau sett på turen og om noen lam manglet.</p>
	                <span class="logout-div">
					    <button id="reportBtn" type="submit" class="btn btn-primary">Generer rapport</button>
					</span>
	            </div>
	        </div>
	    </div>

		<div id="map" class="sidebar-map"></div>

	</div>
</body>
<script src="js/script.js"></script>
</html>