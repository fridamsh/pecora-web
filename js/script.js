var mymap = L.map('map').setView([63.416957, 10.402937], 13);
L.tileLayer('http://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=topo2&zoom={z}&x={x}&y={y}', {
    attribution: '<a href="http://www.kartverket.no/">Kartverket</a> | <a href="http://www.ingridogsondre.no" target="_blank">Frida</a>'
}).addTo(mymap);
// Layergroup for adding hike layers
var layer = L.layerGroup().addTo(mymap);
// Sidebar plugin
var sidebar = L.control.sidebar('sidebar').addTo(mymap);
// Geolocate plugin
L.control.locate().addTo(mymap);

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
			var id = obj[k].id;
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

	    	//Make a list for the map items as markers and polylines
			mapItems = [];

			//Decode observation points
			var jsonObservationPoints = JSON.parse(observationPoints);
			var totalSheepCount=0;
			for (var i = 0; i < jsonObservationPoints.length; i++) {
				var latitude = Number(jsonObservationPoints[i].locationPoint.mLatitude);
				var longitude = Number(jsonObservationPoints[i].locationPoint.mLongitude);
				var pointParent = new L.LatLng(latitude, longitude);
				var date = new Date(Number(jsonObservationPoints[i].timeOfObservationPoint));
				var marker = L.marker(pointParent, {icon: redIcon});
				marker.bindPopup("<b>Observasjonspunkt "+jsonObservationPoints[i].pointId+"</b><br>Kl. "+date.format("HH:MM")+"<br>Sau sett: "+jsonObservationPoints[i].sheepCount);
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
					marker.bindPopup("<b>Observasjon "+observationList[j].observationId+"</b><br>Type: "+observationList[j].typeOfObservation+"<br>Antall: "+observationList[j].sheepCount);
					// Add polyline between observation point and observation
					var line = new L.Polyline([pointParent,pointChild], {
					    color: 'blue',
					    weight: 3,
					    opacity: 0.8,
					    smoothFactor: 1
					});
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
			    color: 'red',
			    weight: 4,
			    opacity: 0.8,
			    smoothFactor: 1
			}).bindPopup('<b>'+title+'</b><br>'+dateStart.format("dd/mm/yyyy HH:MM")+'-'+dateEnd.format('HH:MM')+'<br><b>Gjeter:</b> '+name+'<br><b>Deltakere:</b> '+participants+'<br><b>Antall sau sett:</b> '+totalSheepCount+'<br><b>Vær:</b> '+weather+'<br><b>Distanse:</b> '+distance+'<br><b>Detaljer:</b> '+description);
			mapItems.push(trackPolyline);
			//mymap.fitBounds(trackPolyline.getBounds());

			//Add all map items to the map
			//var layer = L.layerGroup(mapItems).addTo(mymap);

			//Add hikes to dates page
			var newHTML = '<li><input type="checkbox" id="'+id+'"/> '+title+' '+dateStart.format("dd/mm/yyyy HH:MM")+'</li>';
			$("#hikes-list").append(newHTML);
    	}
	}
};
oReq.open("get", "includes/get-data.inc.php", true);
oReq.send();

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
				showHikeOnMap(hike);

			},
			error: function(xhr, ajaxOptions, thrownError){
				alert("Error");
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
	    }// it's the marker
	});
}

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

	//Decode observation points
	var jsonObservationPoints = JSON.parse(observationPoints);
	var totalSheepCount=0;
	for (var i = 0; i < jsonObservationPoints.length; i++) {
		var latitude = Number(jsonObservationPoints[i].locationPoint.mLatitude);
		var longitude = Number(jsonObservationPoints[i].locationPoint.mLongitude);
		var pointParent = new L.LatLng(latitude, longitude);
		var date = new Date(Number(jsonObservationPoints[i].timeOfObservationPoint));
		var marker = L.marker(pointParent, {icon: redIcon});
		marker.bindPopup("<b>Observasjonspunkt "+jsonObservationPoints[i].pointId+"</b><br>Kl. "+date.format("HH:MM")+"<br>Sau sett: "+jsonObservationPoints[i].sheepCount);
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
			marker.bindPopup("<b>Observasjon "+observationList[j].observationId+"</b><br>Type: "+observationList[j].typeOfObservation+"<br>Antall: "+observationList[j].sheepCount);
			// Add polyline between observation point and observation
			var line = new L.Polyline([pointParent,pointChild], {
			    color: 'blue',
			    weight: 3,
			    opacity: 0.8,
			    smoothFactor: 1
			});
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
	    color: 'red',
	    weight: 4,
	    opacity: 0.8,
	    smoothFactor: 1
	}).bindPopup('<b>'+title+'</b><br>'+dateStart.format("dd/mm/yyyy HH:MM")+'-'+dateEnd.format('HH:MM')+'<br><b>Gjeter:</b> '+name+'<br><b>Deltakere:</b> '+participants+'<br><b>Antall sau sett:</b> '+totalSheepCount+'<br><b>Vær:</b> '+weather+'<br><b>Distanse:</b> '+distance+'<br><b>Detaljer:</b> '+description);
	mapItems.push(trackPolyline);
	mymap.fitBounds(trackPolyline.getBounds());

	//Add all map items to the map
	var layer2 = L.layerGroup(mapItems);
	layer2.id = id;
	layer.addLayer(layer2);
}

function locateUser() {
	mymap.locate({setView : true});
}

$('#location').on('click', function() {
   locateUser();
});
