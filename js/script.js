var mymap = L.map('map').setView([63.416957, 10.402937], 13);
L.tileLayer('http://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=norges_grunnkart&zoom={z}&x={x}&y={y}', {
    attribution: '<a href="https://www.kartverket.no/">Kartverket</a> | <a href="http://www.ingridogsondre.no" target="_blank">Frida</a>'
}).addTo(mymap);

/*var kartverket_topo2 = 'https://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=topo2&zoom={z}&x={x}&y={y}',
	kartverket_grunnkart = 'http://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=norges_grunnkart&zoom={z}&x={x}&y={y}',
	kartverket_attribution = '<a href="https://www.kartverket.no/">Kartverket</a> | <a href="http://www.ingridogsondre.no" target="_blank">Frida</a>';

var topo2 = L.tileLayer(kartverket_topo2, {attribution: kartverket_attribution}),
    grunnkart   = L.tileLayer(kartverket_grunnkart, {attribution: kartverket_attribution});

var mymap = L.map('map', {
    center: [63.416957, 10.402937],
    zoom: 13,
    layers: [topo2, grunnkart]
});*/

// Layergroup for adding hike layers
var layer = L.layerGroup().addTo(mymap);
// Sidebar plugin
var sidebar = L.control.sidebar('sidebar').addTo(mymap);
// Geolocate plugin
L.control.locate().addTo(mymap);

var name = '', lastname = '', email = '', username = '';

$.ajax({
	url: "includes/get-user.inc.php",
	type: "GET",
	success: function (data) {
		var user = JSON.parse(data);
		//Check if there was an error reading from the database
		if (user.error == 'error') {
			alert("Ingen turer å vise");
		} else {
			name = user.name;
			lastname = user.lastname;
			email = user.email;
			username = user.username;
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
		var observationList = jsonObservationPoints[i].observationList;
		var marker = L.marker(pointParent, {icon: observationPointIcon});
		marker.setOpacity(0.5);
		if (observationList.length == 1) {
			marker.bindPopup("<b>Punkt "+jsonObservationPoints[i].pointId+" kl. "+date.format("HH:MM")+
			"</b><br><i>"+observationList.length+" observasjon</i>"+
			"<br><b>Sau sett:</b> "+jsonObservationPoints[i].sheepCount);
		} else {
			marker.bindPopup("<b>Punkt "+jsonObservationPoints[i].pointId+" kl. "+date.format("HH:MM")+
			"</b><br><i>"+observationList.length+" observasjoner</i>"+
			"<br><b>Sau sett:</b> "+jsonObservationPoints[i].sheepCount);
		}
		mapItems.push(marker);
		totalSheepCount+=Number(jsonObservationPoints[i].sheepCount);
		//Decode observations
		for (var j = 0; j < observationList.length; j++) {
			var latitude = Number(observationList[j].locationObservation.mLatitude);
			var longitude = Number(observationList[j].locationObservation.mLongitude);
			var pointChild = new L.LatLng(latitude, longitude);
			// Make marker for observation
			var marker = L.marker(pointChild, {icon: blueIcon});
			if (observationList[j].typeOfObservation == 'Sau') {
				marker.bindPopup("<b>Observasjon "+observationList[j].observationId+
				"</b><br><b>Type:</b> "+observationList[j].typeOfObservation+
				"<br><b>Antall:</b> "+observationList[j].sheepCount+
				"<br><b>Detaljer:</b> "+observationList[j].details);
			} else {
				marker.bindPopup("<b>Observasjon "+observationList[j].observationId+
				"</b><br><b>Type:</b> "+observationList[j].typeOfObservation+
				"<br><b>Detaljer:</b> "+observationList[j].details);
			}
			mapItems.push(marker);
			// Add polyline between observation point and observation
			var line = new L.Polyline([pointParent,pointChild], {
			    color: polylineColor,
			    weight: 3,
			    opacity: 0.4,
			    smoothFactor: 1
			}).bindPopup('<b>Punkt '+jsonObservationPoints[i].pointId+' kl. '+date.format("HH:MM")+
				'</b><br><i>Observasjon '+observationList[j].observationId+
				'</i><br><b>Type:</b> '+observationList[j].typeOfObservation+
				'<br><b>Sau sett:</b> '+observationList[j].sheepCount);
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
	    smoothFactor: 1,
	    renderer: L.canvas()
	}).bindPopup('<b>'+title+'</b><br>'+dateStart.format("dd/mm/yyyy HH:MM")+'-'+dateEnd.format('HH:MM')+
		'<br><b>Gjeter:</b> '+name+
		'<br><b>Deltakere:</b> '+participants+
		'<br><b>Antall sau sett:</b> '+totalSheepCount+
		'<br><b>Vær:</b> '+weather+
		'<br><b>Distanse:</b> '+distance+' km'+
		'<br><b>Detaljer:</b> '+description);
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
var observationPointIcon = L.icon({
    iconUrl: 'img/circle.png',
    iconSize: [10, 10],
    iconAnchor: [5, 4],
    popupAnchor: [0, 0]
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

var imgData = 'data:image/png;base64,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';

$('#reportBtn').on('click', function() {
	// Default export is a4 paper, portrait, using milimeters for units
	$.ajax({
		url: "includes/get-report-information.inc.php",
		type: "GET",
		success: function (data) {
			//Parse data
			var obj = JSON.parse(data);
			if (obj.error == 'error') {
				alert("Ingen turer å vise i rapport");
			} else {
				//Create PDF
				var doc = new jsPDF();
				pageHeight= doc.internal.pageSize.height;

				doc.setFontSize(12);
				var dateNow = new Date();
				var dateNowFormatted = dateNow.format("dd/mm/yyyy");
				doc.text(195, 15, dateNowFormatted, null, null, 'right');
				doc.setFontSize(20);
				doc.text(105, 25, 'Pecora', null, null, 'center');
				doc.setFontSize(22);
				doc.setFontType('bold');
				doc.text(105, 37, 'Generert Rapport', null, null, 'center');
				doc.addImage(imgData, 'PNG', 95, 44, 20, 20);

				doc.setFontType('normal');
				doc.setFontSize(14);
				doc.text('Beitelag: Ukjent', 15, 75);
				doc.text('Beiteår: '+dateNow.format('yyyy'), 15, 85);
				doc.text('Tilsynsperson: '+name+' '+lastname, 15, 95);
				var lineUnit = 110;
				//Loop through hikes list
				for (var i = 0; i < obj.length; i++) {
					var startdate = obj[i].startdate;
					var enddate = obj[i].enddate;
					var description = obj[i].description;
			    	var observationPoints = obj[i].observationPoints;
			    	//Decode observation points
					var jsonObservationPoints = JSON.parse(observationPoints);
					var totalSheepCount=0;
					for (var j = 0; j < jsonObservationPoints.length; j++) {
						totalSheepCount+=Number(jsonObservationPoints[j].sheepCount);
					}
			    	var dateStart = new Date(Number(startdate));
			    	var dateEnd = new Date(Number(enddate));
					//Add text to PDF
					// Before adding new content
					if (lineUnit+30 >= pageHeight) {
						doc.addPage();
						lineUnit = 20 // Restart height position 
					}
					doc.setFontType('bold');
					doc.text('Dato: '+dateStart.format("dd/mm/yyyy")+'		Beskrivelse: '+description, 15, lineUnit);
					lineUnit+=10;
					doc.setFontType('normal');
					doc.text('Antall sau sett: '+totalSheepCount, 15, lineUnit);
					lineUnit+=20;
		    	}
		    	//Last ned PDF
		    	var dateNowFormattedDash = dateNow.format("dd-mm-yyyy");
		    	var pdfName = 'report-'+dateNowFormattedDash+'.pdf';
				doc.save(pdfName);

				//cover.className = 'active';
                //leafletImage(mymap, downloadMap);
			}
		},
		error: function(xhr, ajaxOptions, thrownError){
			alert("AJAX Error");
		},
		timeout: 15000 //timeout of the ajax call
	});
});

function downloadMap(err, canvas) {
    var imgData = canvas.toDataURL("image/svg+xml", 1.0);
    var dimensions = mymap.getSize();
    
    var pdf = new jsPDF('l', 'pt', 'letter');
    pdf.addImage(imgData, 'PNG', 10, 10, dimensions.x * 0.5, dimensions.y * 0.5);
    
    //cover.className = '';
    pdf.save("download.pdf");
};


