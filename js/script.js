var mymap = L.map('map').setView([63.416957, 10.402937], 13);
L.tileLayer('https://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=topo2&zoom={z}&x={x}&y={y}', {
    attribution: '<a href="https://www.kartverket.no/">Kartverket</a> | <a href="http://www.ingridogsondre.no" target="_blank">Frida</a>'
}).addTo(mymap);
// Layergroup for adding hike layers
var layer = L.layerGroup().addTo(mymap);
// Sidebar plugin
var sidebar = L.control.sidebar('sidebar').addTo(mymap);
// Geolocate plugin
L.control.locate().addTo(mymap);

$.ajax({
	url: "includes/get-user.inc.php",
	type: "GET",
	success: function (data) {
		var user = JSON.parse(data);
		//Check if there was an error reading from the database
		if (user.error == 'error') {
			alert("Ingen turer å vise");
		} else {
			//Make html to append to profile page
			var newHTML = '<p><b>Navn </b>' + user.name + ' ' + user.lastname + '</p>' + '<p><b>Email </b>' + user.email + '</p>' + 
			'<p><b>Brukernavn </b>' + user.username + '</p>';
			//Append html to profile data div
			$("#profile-data").append(newHTML);
		}
	},
	error: function(xhr, ajaxOptions, thrownError){
		alert("AJAX Error - Kunne ikke laste inn brukerdata");
	},
	timeout: 15000 //timeout of the ajax call
});

$(function () {
    $('#datetimepicker1').datetimepicker();
    $('#datetimepicker2').datetimepicker({
        useCurrent: false //Important! See issue #1075
    });
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });
});

function getMostRecentHikes() {
	$.ajax({
		url: "includes/get-data.inc.php",
		type: "GET",
		success: function (data) {
			var obj = JSON.parse(data);
			if (obj.error == 'error') {
				var newHTML = '<li><i>Ingen turer å vise</i></li>';
				$("#hikes-list").append(newHTML);
			} else {
				for (var k = 0; k < obj.length; k++) {
					var id = obj[k].id;
					var title = obj[k].title;
			    	var startdate = obj[k].startdate;
			    	var dateStart = new Date(Number(startdate));
			    	if (k == 0) {
			    		$('#datetimepicker2').datetimepicker({
				            defaultDate: dateStart
				        });
			    	}
			    	if (k == obj.length-1) {
			    		$('#datetimepicker1').datetimepicker({
				            defaultDate: dateStart
				        });
			    	}
					//Add hikes to the dates page
					var newHTML = '<li><input type="checkbox" id="'+id+'"/> '+title+' '+dateStart.format("dd/mm/yyyy HH:MM")+'</li>';
					$("#hikes-interval-list").append(newHTML);
		    	}
			}
		},
		error: function(xhr, ajaxOptions, thrownError){
			alert("AJAX Error - Kunne ikke laste inn turdata");
		},
		timeout: 15000 //timeout of the ajax call
	});
}

getMostRecentHikes();

function createRandomColor() {
	//Defined a list with some nice colors, can add here if I want
	var colors = ['rgb(255, 0, 0)', 'rgb(255, 102, 0)', 'rgb(255, 0, 102)', 'rgb(204, 0, 153)', 
	'rgb(204, 102, 0)', 'rgb(255, 255, 0)', 'rgb(255, 204, 0)', 'rgb(204, 255, 51)', 'rgb(153, 255, 51)', 
	'rgb(102, 153, 0)', 'rgb(0, 153, 0)', 'rgb(51, 204, 51)', 'rgb(0, 204, 102)', 'rgb(0, 204, 153)', 
	'rgb(0, 153, 153)', 'rgb(0, 255, 204)', 'rgb(51, 204, 204)', 'rgb(0, 102, 153)', 'rgb(0, 204, 255)', 
	'rgb(0, 51, 204)', 'rgb(0, 102, 255)', 'rgb(102, 102, 255)', 'rgb(102, 0, 204)', 'rgb(204, 102, 255)'];
	var random = Math.floor(Math.random() * Math.floor(colors.length));
	return colors[random];
}

/*function makeColorDarker(color) {
	alert(color);
}*/

function showHikeOnMap(hike) {
	var id = hike.id;
	var title = hike.title;
	var name = hike.name;
	var participants = hike.participants;
	var weather = hike.weather;
	var description = hike.description;
	var startdate = hike.startdate;
	var dateStart = new Date(Number(startdate));
	var enddate = hike.enddate;
	var dateEnd = new Date(Number(enddate));
	var mapfile = hike.mapfile;
	var distance = hike.distance;
	var userId = hike.userId;
	var localId = hike.localId;
	var observationPoints = hike.observationPoints;
	var track = hike.track;

	mapItems = [];
	var polylineColor = createRandomColor();
	//var darkerPolylineColor = makeColorDarker(polylineColor);

	//Decode observation points
	var jsonObservationPoints = JSON.parse(observationPoints);
	var totalSheepCount=0;
	for (var i = 0; i < jsonObservationPoints.length; i++) {
		var latitude = Number(jsonObservationPoints[i].locationPoint.mLatitude);
		var longitude = Number(jsonObservationPoints[i].locationPoint.mLongitude);
		var pointParent = new L.LatLng(latitude, longitude);
		var date = new Date(Number(jsonObservationPoints[i].timeOfObservationPoint));
		var marker = L.marker(pointParent, {icon: darkBlueIcon});
		marker.bindPopup("<b>Punkt "+jsonObservationPoints[i].pointId+"</b><br>Kl. "+date.format("HH:MM")+"<br>Sau sett: "+
			jsonObservationPoints[i].sheepCount);
		mapItems.push(marker);
		totalSheepCount+=Number(jsonObservationPoints[i].sheepCount);
		//Decode observations
		var observationList = jsonObservationPoints[i].observationList;
		for (var j = 0; j < observationList.length; j++) {
			var latitude = Number(observationList[j].locationObservation.mLatitude);
			var longitude = Number(observationList[j].locationObservation.mLongitude);
			var pointChild = new L.LatLng(latitude, longitude);
			var marker = L.marker(pointChild, {icon: blueIcon});
			mapItems.push(marker);
			marker.bindPopup("<b>Observasjon "+observationList[j].observationId+"</b><br>Type: "+observationList[j].typeOfObservation+
				"<br>Antall: "+observationList[j].sheepCount);
			// Add polyline between observation point and observation
			var line = new L.Polyline([pointParent,pointChild], {
			    color: polylineColor,
			    weight: 3,
			    opacity: 0.4,
			    smoothFactor: 1
			}).bindPopup('<b>Punkt '+jsonObservationPoints[i].pointId+' kl. '+date.format("HH:MM")+'</b><br>Observasjon '+
			observationList[j].observationId+'<br><b>Sau sett:</b> '+observationList[j].sheepCount+'<br><b>Type:</b> '+
			observationList[j].typeOfObservation);
			mapItems.push(line);
		}
	}

	//Decode track
	trackPointList = [];
	var jsonTrack = JSON.parse(track);
	for (var i = 0; i < jsonTrack.length; i++) {
		var latitude = Number(jsonTrack[i].mLatitude);
		var longitude = Number(jsonTrack[i].mLongitude);
		var point = new L.LatLng(latitude, longitude);
		trackPointList.push(point);
	}
	
	var markerStart = L.marker(trackPointList[0], {icon: startIcon});
	markerStart.bindPopup("<b>Start</b> "+dateStart.format("HH:MM"));
	mapItems.push(markerStart);
	var markerEnd = L.marker(trackPointList[trackPointList.length-1], {icon: stopIcon});
	markerEnd.bindPopup("<b>Slutt</b> "+dateEnd.format("HH:MM"));
	mapItems.push(markerEnd);

	var trackPolyline = new L.Polyline(trackPointList, {
	    color: polylineColor,
	    weight: 4,
	    opacity: 0.9,
	    smoothFactor: 1
	}).bindPopup('<b>'+title+'</b><br>'+dateStart.format("dd/mm/yyyy HH:MM")+'-'+dateEnd.format('HH:MM')+'<br><b>Gjeter:</b> '+
	name+'<br><b>Deltakere:</b> '+participants+'<br><b>Antall sau sett:</b> '+totalSheepCount+'<br><b>Vær:</b> '+weather+
	'<br><b>Distanse:</b> '+distance+'<br><b>Detaljer:</b> '+description);
	mapItems.push(trackPolyline);
	mymap.fitBounds(trackPolyline.getBounds());

	//Add all map items to the map
	var layer2 = L.layerGroup(mapItems);
	layer2.id = id;
	layer.addLayer(layer2);
}

$.ajax({
	url: "includes/get-most-recent-hike.inc.php",
	type: "GET",
	success: function (data) {
		var hike = JSON.parse(data);
		if (hike.error == 'error') {
			alert("Ingen nylig tur å vise");
		} else {
			//Get checkbox id for the hike and check it
			var checkboxId = '#'+hike.id;
			$(checkboxId).prop('checked', true);
			//Show the hike on the map
			showHikeOnMap(hike);
		}
	},
	error: function(xhr, ajaxOptions, thrownError){
		alert("AJAX Error - Kunne ikke laste inn turdata");
	},
	timeout: 15000 //timeout of the ajax call
});

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
    iconSize: [20, 36],
    iconAnchor: [10, 35],
    popupAnchor: [0, -28],
    shadowUrl: 'img/marker-shadow.png',
    shadowSize: [36, 36],
    shadowAnchor: [11, 35]
});
var darkBlueIcon = L.icon({
    iconUrl: 'img/marker-icon-2x-dark-blue.png',
    iconSize: [25, 41],
    iconAnchor: [13, 40],
    popupAnchor: [0, -33],
    shadowUrl: 'img/marker-shadow.png',
    shadowSize: [41, 41],
    shadowAnchor: [13, 40]
});

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

$('#hikes-list').on('change', 'input[type="checkbox"]', function() {
    var hikeId = $(this).attr('id');
    if (this.checked) {
    	var json = {"hikeId": hikeId};
    	$.ajax({
			url: "includes/get-hike.inc.php",
			type: "POST",
			data: json,
			success: function (data) {
				//Show the checked hike on the map
				var hike = JSON.parse(data);
				if (hike.error == 'error') {
					alert("Ingen tur å vise");
				} else {
					showHikeOnMap(hike);
				}
			},
			error: function(xhr, ajaxOptions, thrownError){
				alert("AJAX Error");
			},
			timeout: 15000 //timeout of the ajax call
		});
    } else {
    	// Hide the unchecked hike
    	removeHikeFromMap(hikeId);
    }
});

function removeHikeFromMap(hikeId) {
	//Remove the hike from the map
	layer.eachLayer(function (layerInGroup) {
	    if (layerInGroup.id === hikeId) {
	    	layer.removeLayer(layerInGroup);
	    }
	});
}

function removeHikesFromMap() {
	//Remove the hike from the map
	layer.eachLayer(function (layerInGroup) {
		layer.removeLayer(layerInGroup);
	});
}

$('#intervalBtn').on('click', function() {
	//Reset list
	$("#hikes-interval-list").html("");
	//Remove layers from map
	removeHikesFromMap();
	//Get dates from date pickers
	var fromDate = new Date($('#datetimepicker1').data("DateTimePicker").date()).getTime();
	var toDate = new Date($('#datetimepicker2').data("DateTimePicker").date()).getTime();
	//Create post data
	var json = {"from": fromDate, "to": toDate};
	$.ajax({
		url: "includes/get-hike-interval.inc.php",
		type: "POST",
		data: json,
		success: function (data) {
			var obj = JSON.parse(data);
			if (obj.error == 'error') {
				alert("Ingen turer å vise i dette intervallet");
			} else {
				//Show hikes within interval in a list
				for (var i = 0; i < obj.length; i++) {
					var id = obj[i].id;
					var title = obj[i].title;
			    	var startdate = obj[i].startdate;
			    	var dateStart = new Date(Number(startdate));
					//Add hikes to the dates page
					var newHTML = '<li><input type="checkbox" id="'+id+'"/> '+title+' '+dateStart.format("dd/mm/yyyy HH:MM")+'</li>';
					$("#hikes-interval-list").append(newHTML);
		    	}
			}
		},
		error: function(xhr, ajaxOptions, thrownError){
			alert("AJAX Error");
		},
		timeout: 15000 //timeout of the ajax call
	});
});

$('#hikes-interval-list').on('change', 'input[type="checkbox"]', function() {
	//Get the id of the checkbox which is the hike id
    var hikeId = $(this).attr('id');
    //If it is checked, retrieve hike data from database
    if (this.checked) {
    	var json = {"hikeId": hikeId};
    	$.ajax({
			url: "includes/get-hike.inc.php",
			type: "POST",
			data: json,
			success: function (data) {
				//Show the checked hike on the map
				var hike = JSON.parse(data);
				if (hike.error == 'error') {
					alert("Ingen tur å vise");
				} else {
					showHikeOnMap(hike);
				}
			},
			error: function(xhr, ajaxOptions, thrownError){
				alert("AJAX Error");
			},
			timeout: 15000 //timeout of the ajax call
		});
    } else {
    	//Hide the unchecked hike
    	removeHikeFromMap(hikeId);
    }
});

$('#resetBtn').on('click', function() {
	//Reset hikes interval list and regenerate the default list
	$("#hikes-interval-list").html("");
	//Remove hikes from map
	removeHikesFromMap();
	//Get the most recent hikes
	getMostRecentHikes();
});
