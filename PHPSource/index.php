<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<title>PMAPS: PWILD Marking/Annotating Pisgah Systematically</title>
<style type="text/css">
html { overflow: hidden; }
body { overflow: hidden; padding: 0; margin: 0;
width: 100%; height: 100%; font-family: Trebuchet MS, Trebuchet, Arial, sans-serif; }
#map {position: absolute; top: 130px; left: 25px; right: 410px; bottom: 35px; overflow: auto;}
@media screen and (max-width: 600px) {
  #map { top:0px; left:0px; width:100%; height:100%;}
}
body { background: #f4f4f4;}
#map { border: 1px solid #ccc; box-shadow: 0 1px 3px #CCC; background-color: #DEDCD7;}
#footer { text-align:center; font-size:9px; color:#606060; }
header {
  padding: 20px;
  text-align: center;
  background: #333;
  color: white;
  font-size: 20px;
}
ul {
    position: fixed;
    top: 10;
    width: 100%;
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #A9A9A9;
}

li {
    float: left;
}

li a {
    display: block;
    color: white;
    outline: none;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    background-color: #A9A9A9;
}

.headerButton {
	display: block;
    color: white;
    outline: none;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    background-color: #A9A9A9;	
}




table {
	position: absolute;
    right: 5px;
    width: 98%;
  }

th, td {
    text-align: left;
    padding: 8px;
    border: 1px solid black;
}

h3 {
	text-align: center;
}

tr:nth-child(even) {background-color: #f2f2f2;}



.body {
  overflow: hidden;
  height: 500px;
  overflow-y: auto;
  position: absolute;
  right: 0px;
  top: 480px;
  border-collapse: collapse;
  width: 400px;
}

.filter {
	height: 300px;
	position: absolute;
	right: 0px;
	top: 130px;
	border-collapse: collapse;
  	width: 400px;
}


#cbbutton{
  float: right;
  width: 100px;
  border: 3px solid #73AD21;
  padding: 10px;
	}
</style>

<?php
	try {
		include("/etc/php/my-pdo.php");
		$dbh = dbconnect();
	} catch (PDOException $e) {
		print "Error connecting to the database: " . $e->getMessage() . "<br/>";
		die();
	}
	$comment_array = $dbh->query('SELECT cid,name,text,type,timestamp,longitude,lattitude FROM Comment,Person,Place WHERE Comment.pid = Person.pid AND Comment.lid = Place.lid');
?>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDfSBibzyTDahkTrbF19v4Ch9sP_96gb-U&libraries=drawing">
    </script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ["map", "table"]});
    </script>
<script src="jquery-3.3.1.min.js"></script>
<script type="text/javascript">
	var map;
	var mapBounds = new google.maps.LatLngBounds(
		new google.maps.LatLng(35, -84), //sw
		new google.maps.LatLng(35.8, -82)); //ne
	var mapBoundsUSGS2013 = new google.maps.LatLngBounds(
		new google.maps.LatLng(35.25, -83),
		new google.maps.LatLng(35.5, -82.625));
	var mapBoundsUSGSTiff = new google.maps.LatLngBounds(
		new google.maps.LatLng(35.10503944154214, -83.01039847222899),
		new google.maps.LatLng(35.50779134029307, -82.61437567559813));
	var mapBoundsUSGSStolen = new google.maps.LatLngBounds(
		new google.maps.LatLng(34.88696759970738, -83.31711443749998),
		new google.maps.LatLng(35.746423376157786, -82.2638002285156));
	var mapBoundsNatGeo = new google.maps.LatLngBounds(
		new google.maps.LatLng(35, -84),
		new google.maps.LatLng(35.81772435462227, -81.97995814172361));
	var usgs2013 = new google.maps.ImageMapType({
		minZoom: 11,
		maxZoom: 15,
		getTileUrl: function(coord, zoom) { 
			var proj = map.getProjection();
			var z2 = Math.pow(2, zoom);
			var tileXSize = 256 / z2;
			var tileYSize = 256 / z2;
			var tileBounds = new google.maps.LatLngBounds(
				proj.fromPointToLatLng(new google.maps.Point(coord.x * tileXSize, (coord.y + 1) * tileYSize)),
				proj.fromPointToLatLng(new google.maps.Point((coord.x + 1) * tileXSize, coord.y * tileYSize))
			);
			var y = coord.y;
			if (mapBoundsUSGS2013.intersects(tileBounds) && (11 <= zoom) && (zoom <= 15))
				return "images/usgs2013" + "/" + zoom + "/" + coord.x + "/" + y + ".png";
			else
				return "images/none.png";
		},
		tileSize: new google.maps.Size(256, 256),
		isPng: true,
		name: "USGS 2013",
		alt: "USGS 2013"
	});
	var usgsStolen = new google.maps.ImageMapType({
		minZoom: 10,
		maxZoom: 16,
		getTileUrl: function(coord, zoom) { 
			var proj = map.getProjection();
			var z2 = Math.pow(2, zoom);
			var tileXSize = 256 / z2;
			var tileYSize = 256 / z2;
			var tileBounds = new google.maps.LatLngBounds(
				proj.fromPointToLatLng(new google.maps.Point(coord.x * tileXSize, (coord.y + 1) * tileYSize)),
				proj.fromPointToLatLng(new google.maps.Point((coord.x + 1) * tileXSize, coord.y * tileYSize))
			);
			var y = coord.y;
			if (mapBoundsUSGSStolen.intersects(tileBounds) && (10 <= zoom) && (zoom <= 16))
				return "images/usgsStolen" + "/" + zoom + "/" + coord.x + "/" + y + ".png";
			else
				return "images/none.png";
		},
		tileSize: new google.maps.Size(256, 256),
		isPng: true,
		name: "USGS",
		alt: "USGS"
	});
	var usgsTiff = new google.maps.ImageMapType({
		minZoom: 11,
		maxZoom: 15,
		getTileUrl: function(coord, zoom) { 
			var proj = map.getProjection();
			var z2 = Math.pow(2, zoom);
			var tileXSize = 256 / z2;
			var tileYSize = 256 / z2;
			var tileBounds = new google.maps.LatLngBounds(
				proj.fromPointToLatLng(new google.maps.Point(coord.x * tileXSize, (coord.y + 1) * tileYSize)),
				proj.fromPointToLatLng(new google.maps.Point((coord.x + 1) * tileXSize, coord.y * tileYSize))
			);
			var y = coord.y;
			if (mapBoundsUSGSTiff.intersects(tileBounds) && (11 <= zoom) && (zoom <= 15))
				return "images/usgsTiff" + "/" + zoom + "/" + coord.x + "/" + y + ".png";
			else
				return "images/none.png";
		},
		tileSize: new google.maps.Size(256, 256),
		isPng: true,
		name: "USGS TIFF",
		alt: "USGS TIFF"
	});
	var natGeo = new google.maps.ImageMapType({
		minZoom: 12,
		maxZoom: 14,
		getTileUrl: function(coord, zoom) { 
			var proj = map.getProjection();
			var z2 = Math.pow(2, zoom);
			var tileXSize = 256 / z2;
			var tileYSize = 256 / z2;
			var tileBounds = new google.maps.LatLngBounds(
				proj.fromPointToLatLng(new google.maps.Point(coord.x * tileXSize, (coord.y + 1) * tileYSize)),
				proj.fromPointToLatLng(new google.maps.Point((coord.x + 1) * tileXSize, coord.y * tileYSize))
			);
			var y = coord.y;
			if (mapBoundsNatGeo.intersects(tileBounds) && (12 <= zoom) && (zoom <= 14))
				return "images/natGeo" + "/" + zoom + "/" + coord.x + "/" + y + ".jpg";
			else
				return "images/none.png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Nat Geo",
		alt: "Nat Geo",
		isPng: true
	});
	var trailLayer;
	var markerLayer;
	var usersMarker;
	var commentArray = {
		comInternal: null,
		comListener: function(val) { updateCommentList(val); },
		set list(val) {
			this.comInternal = val;
			this.comListener(val);
		},
		get list() {
			return this.comInternal;
		}
	};
	var infoWindow;
	var drawingManager;
	var recBounds;
	function init() {
		recBounds = null;
		var opts = {
			streetViewControl: true,
			mapTypeId: 'Nat Geo',
			backgroundColor: "rgb(220,220,220)",
			center: new google.maps.LatLng(35.292621, -82.833716),
			zoom: 13,
			mapTypeControlOptions: {
				mapTypeIds: [
					'USGS 2013', 'USGS TIFF', 'Nat Geo', 'USGS',
					google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE,
					google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.TERRAIN
				]
			},
		}
		map = new google.maps.Map(document.getElementById("map"), opts);
		map.setTilt(45);
		map.mapTypes.set('USGS 2013', usgs2013);
		map.mapTypes.set('USGS TIFF', usgsTiff);
		map.mapTypes.set('Nat Geo', natGeo);
		map.mapTypes.set('USGS', usgsStolen);
		
		var drawingManager = new google.maps.drawing.DrawingManager({
		    drawingMode: null,
		    drawingControl: true,
		    drawingControlOptions: {
		      	position: google.maps.ControlPosition.TOP_RIGHT,
		      	drawingModes: ['rectangle']
		    },
		    rectangleOptions: {
		      	fillColor: '#00bb00',
		      	fillOpacity: .5,
		      	strokeWeight: 5,
		      	clickable: false,
		      	editable: true,
		      	zIndex: 1,
		      	draggable: true
		    }
		});
		drawingManager.setMap(map);

		google.maps.event.addListener(drawingManager, 'rectanglecomplete', function(rectangle) {
			if(recBounds) {
				recBounds.setMap(null);
			}
			recBounds = rectangle;
			filterCommentsFromForm();
			rectangle.addListener('bounds_changed', function() {
				filterCommentsFromForm();
			});		
		});

		
		// A data layer to hold all the data people have added
		markerLayer = new google.maps.Data();

		commentArray.list = <?php echo json_encode($comment_array->fetchAll(PDO::FETCH_ASSOC)); ?>;
		commentArray.list.forEach(function(comment) {
			var geowanted = new google.maps.Data.Point({lat: parseFloat(comment.longitude), lng: parseFloat(comment.lattitude)});		// Issue here: lat and lng are switched
			var propswanted = {cid: comment.cid, name: comment.name, description: comment.text, category: comment.type, date: comment.timestamp};
			markerLayer.add({geometry: geowanted, properties: propswanted});
		});

		// Color the data layer based on the category of the data, and give each point roll over text
		markerLayer.setStyle(function(feature) { 
			return ({icon: getDot(feature.getProperty('category')),
					title: feature.getProperty('description')}); 
		});
		
		// When the data layer is clicked on, display the appropriate data
		infowindow = new google.maps.InfoWindow();
		markerLayer.addListener('click', function(event) { 
			var timeStamp = event.feature.getProperty('date');
			var markerDescription = event.feature.getProperty('description');
			var contributorName = event.feature.getProperty('name');
		
			var content = "<div class='googft-info-window'>"+
						"<p style='font-size:14px'>"+markerDescription+"</p><br><br>"+
						"<b>Contributed By:</b> "+contributorName+"<br>"+
						"<i>"+timeStamp+"</i><br></div>";
			
			infowindow.setContent(content);
			infowindow.setPosition(event.feature.getGeometry().get());
			infowindow.open(map);
		});
		
		// Put the data layer on the map
		markerLayer.setMap(map);
		
		// Makes a big red pin
		usersMarker = new google.maps.Marker({
			position: map.getCenter(),
			map: null,
			title: 'Use me to mark places',
			draggable: true,
			animation: google.maps.Animation.DROP,
			anchorPoint: new google.maps.Point(0,-30)
		});
		
		infoWindow = new google.maps.InfoWindow(); // Makes an info window for showing the data input form.
		
		//google.maps.event.addListener(usersMarker, 'click', showInputFormWindow); // When the user clicks on the big red pin, open up the form for data input.
		
		//google.maps.event.addListener(usersMarker, 'position_changed', function() {infoWindow.close();}); // When the user moves the big red pin, close the data input form. They clearly don't need it. You know what? We don't need them either!
		
		// Trails Stuff
		trailLayer = new google.maps.FusionTablesLayer({
			  query: {
				select: 'ns1:coordinates',
				from: '1Rk-HToZ45a3mpLbWfBf9sY_5cmaVP0ibBFVk_GA',
				where: 'id3 > 1'
			  },
			  map: null,
			  suppressInfoWindows: true,
			  styles: [{
				polylineOptions:{
					strokeWeight: 1
				}
			  }]
			});
			google.maps.event.addListener(trailLayer, 'click', function(e) {
			  windowControlTrail(e, infoWindow, map);
			});		
		var trailControlDiv = document.createElement('div');
		var trailControl = new TrailControl(trailControlDiv, map);
		trailControlDiv.index = 1;
		map.controls[google.maps.ControlPosition.TOP_RIGHT].push(trailControlDiv);
	}

	function cancelData(){
		usersMarker.setMap(null);
		filterCommentsFromForm();
	}

	function saveData(){ //What to do when the user hits the "submit" button on the user input form.
		// Awkwardly pulls what the user wrote on the input form
		var name = document.getElementById("nameField").value;
		var description = document.getElementById("textField").value;
		var cat = document.getElementById("typeField").value;
		var pos = usersMarker.getPosition();

		if(name == "" || description == "" || cat == "") {
			alert("Please fill all fields.");
			return;
		}

		$.post("saveData.php",
		{
			name: name,
			text: description,
			type: cat,
			longitude: pos.lng(),
			lattitude: pos.lat(),
		},
		function(data,status) {
			location.reload();
			alert(data);
		});
						
		usersMarker.setMap(null);
	}


	function startComment() 
	{
		removeAllMarkers();
		usersMarker.setMap(map);
		document.getElementById('commentList').innerHTML = "<div><h3>Submit a Comment</h3>" + 
				"<table>" +
					"<tr><td>Name: </td><td><input type=\"text\" id=\"nameField\"></td>" +
					"<tr><td>Comment: </td><td><textarea id=\"textField\"></textarea></td>" +
					"<tr><td>Category: </td><td><select id=\"typeField\">" +
							"<option value=\"general\">General</option>" +
							"<option value=\"vam\">VAM</option>" +
							"<option value=\"water\">Water</option>" +
							"<option value=\"safety\">Safety</option>" +
							"<option value=\"campsite\">Campsite</option>" +
							"<option value=\"tip\">Tip</option>" +
							"<option value=\"solos\">Solos</option>" +
						"</select></td>" +
						"<tr><td><input type=\"button\" value=\"Submit\" onclick=\"saveData()\"></td>" +
						"<td><input type=\"button\" value=\"Cancel\" onclick=\"cancelData()\"></td>" +
				"</table>" +
			"</div>";
	}

	function removeAllMarkers() 
	{
		markerLayer.forEach(function(feature) {
			markerLayer.remove(feature);
		});
	}

	function resetSelection()
	{
		if(recBounds) {
			recBounds.setMap(null);
		}
		recBounds = null;

		document.getElementById("generalCheck").checked = true;
		document.getElementById("tipCheck").checked = true;
		document.getElementById("solosCheck").checked = true;
		document.getElementById("waterCheck").checked = true;
		document.getElementById("vamCheck").checked = true;
		document.getElementById("safetyCheck").checked = true;
		document.getElementById("campsiteCheck").checked = true;

		document.getElementById("augCheck").checked = true;
		document.getElementById("marchCheck").checked = true;
		document.getElementById("stepCheck").checked = true;

		document.getElementById("nameCheck").value = "";
		document.getElementById("minDateCheck").value = "";
		document.getElementById("maxDateCheck").value = "";

		filterCommentsFromForm();

	}





	function filterCommentsFromForm()
	{
		var types = [];

		if($('#generalCheck').prop('checked')){
			types.push('general');
		}
		if($('#vamCheck').prop('checked')){
			types.push('vam');
		}
		if($('#waterCheck').prop('checked')){
			types.push('water');
		}
		if($('#safetyCheck').prop('checked')){
			types.push('safety');
		}
		if($('#campsiteCheck').prop('checked')){
			types.push('campsite');
		}
		if($('#tipCheck').prop('checked')){
			types.push('tip');
		}
		if($('#solosCheck').prop('checked')){
			types.push('solos');
		}

		var trips = [];

		if($('#augCheck').prop('checked')){
			trips.push('august');
		}
		if($('#marchCheck').prop('checked')){
			trips.push('march');
		}
		if($('#stepCheck').prop('checked')){
			trips.push('step');
		}

		if(trips.length == 3) trips = "";

		if(recBounds != null) {
			var bounds = recBounds.getBounds();
			var sw = bounds.getSouthWest();
			var ne = bounds.getNorthEast();

			filterComments(document.getElementById("nameCheck").value,types,trips, sw.lat(), ne.lat(), sw.lng(), ne.lng(), document.getElementById("minDateCheck").value,document.getElementById("maxDateCheck").value);
		} else {
			filterComments(document.getElementById("nameCheck").value,types,trips, " "," "," "," ",document.getElementById("minDateCheck").value,document.getElementById("maxDateCheck").value);
		}
		
	}

	function filterComments(names, types, trips, minlat, maxlat, minlng, maxlng, mintime, maxtime)	// Time not implemented client side yet 
	{
		removeAllMarkers();

		$.post("filterComments.php",
		{
			name: names,
			type: types,
			trip: trips,
			minlat: minlat,
			maxlat: maxlat,
			minlng: minlng,
			maxlng: maxlng,
			mintime: mintime,
			maxtime: maxtime
		},
		function(data,status) {
			var decoded = $.parseJSON(data);
			decoded.forEach(function(comment) {
				var geowanted = new google.maps.Data.Point({lat: parseFloat(comment.longitude), lng: parseFloat(comment.lattitude)});		// Issue here: lat and lng are switched
				var propswanted = {cid: comment.cid, name: comment.name, description: comment.text, category: comment.type, date: comment.timestamp};
				markerLayer.add({geometry: geowanted, properties: propswanted});
			});
			commentArray.list = decoded;
		});
	}

	function findOnMap(cid) 
	{
		for(var i = 0; i < commentArray.list.length; i++) {
			if(commentArray.list[i].cid == cid) {
				var comment = commentArray.list[i];
				var laln = {lat: parseFloat(comment.longitude), lng: parseFloat(comment.lattitude)};
				map.panTo(laln);
				var content = "<div class='googft-info-window'>"+
							"<p style='font-size:14px'>"+comment.text+"</p><br><br>"+
							"<b>Contributed By:</b> "+comment.name+"<br>"+
							"<i>"+comment.timestamp+"</i><br></div>";
				
				infowindow.setContent(content);
				infowindow.setPosition(laln);
				infowindow.open(map);
				break;
			}	
		}
	}

	function updateCommentList(comments) 
	{
		var contentStr = "<table><tr><th>Name</th><th>Type</th><th>Find</th></tr>";
		comments.forEach(function(comment) {
			contentStr = contentStr + "<tr><td>" + comment.name + "</td><td>" + comment.type + "</td><td><button onclick=\"findOnMap(" + comment.cid + ")\" >Find on Map</button></td></tr>" + 
				"<tr><td colspan=\"3\">" + comment.text + "</td></tr>";
		});
		contentStr = contentStr + "</table>";
		document.getElementById("commentList").innerHTML = contentStr;
	}


	function getDot(category) // Given the category of a data point, spits out the appropriate color of the data point
	{
		if(category == "vam") //purple
			return "images/waterfall.png";
		else if(category == "water") //blue
			return "images/water.png";
		else if(category == "safety") //yellow
			return "images/safety.png";
		else if(category == "campsite")  //green
			return "images/camp.png";
		else if(category == "tip") //red
			return "images/info.png";
		else if(category == "solos")//brown+
			return "images/solo.png";
		else //darkgrey+
			return "images/general.png";
	}	
	function windowControlTrail(e, infoWindow, map) {
		e.infoWindowHtml = "<div class='googft-info-window'>"+
							"<h1>"+e.row['ns3:name'].value+"</h1>"+
							"<i>"+e.row['ns3:segment'].value+"</i>"+
							"<p>"+e.row['Description'].value+"</p>"+
							"<b>Overall Difficulty:</b> "+e.row['Overall Difficulty'].value+"<br>"+
							"<b>Length:</b> "+e.row['Length'].value+"<br>"+
							"<b>Steepness:</b> "+e.row['Steepness'].value+"<br>"+
							"<b>Trail/Tread Condition:</b> "+e.row['Trail/Tread Condition'].value+"<br>"+
							"<b>Blaze Color:</b> "+e.row['Blaze Color'].value+"<br>"+
							"<b>USGS/USFS Number:</b> "+e.row['USGS/USFS Number'].value+"<br>"+
							"<b>Allowed Uses:</b> "+e.row['Allowed Uses'].value+"<br>"+
							"<b>Hikes That Use This Trail:</b> "+e.row['Hikes That Use This Trail'].value+"<br>"+
							"</div>";
		infoWindow.setOptions({
			content: e.infoWindowHtml,
			position: e.latLng,
			pixelOffset: e.pixelOffset
		});
		infoWindow.open(map);
	}
	function TrailControl(controlDiv, map) { //Pertains to the button that controls whether or not to show trails

		// Set CSS styles for the DIV containing the control
		// Setting padding to 5 px will offset the control
		// from the edge of the map
		controlDiv.style.padding = '5px';

		// Set CSS for the control border
		var controlUI = document.createElement('div');
		controlUI.style.backgroundColor = 'white';
		controlUI.style.borderStyle = 'solid';
		controlUI.style.borderWidth = '2px';
		controlUI.style.cursor = 'pointer';
		controlUI.style.textAlign = 'center';
		controlUI.title = 'Click to show/hide the trails';
		controlDiv.appendChild(controlUI);

		// Set CSS for the control interior
		var controlText = document.createElement('div');
		controlText.style.fontFamily = 'Arial,sans-serif';
		controlText.style.fontSize = '12px';
		controlText.style.paddingLeft = '4px';
		controlText.style.paddingRight = '4px';
		controlText.innerHTML = '<b>Trails</b>';
		controlUI.appendChild(controlText);

		// Setup the click event listeners
		google.maps.event.addDomListener(controlUI, 'click', toggleTrails);

	}
	function toggleTrails() // This is to turn on and off the trails when the trails button is clicked
	{
		if(trailLayer.getMap() === map)
			hideTrails();
		else
			showTrails();
	}
	function hideTrails() // This turns off the display of the trails. This function is called when toggleTrails happens.
	{
		trailLayer.setMap(null);
	}
	function showTrails() // This turns on the display of the trails. This function is called when toggleTrails happens.
	{
		trailLayer.setMap(map);
	}
</script>
</head>
<body onload="init()">
<div class="nav-container">
   <nav class="nav-inner transparent">

      <div class="navbar">
         <div class="container">
            <div class="row">

              <div class="brand">
                <header href="index.html">PMAPS: PWILD Marking/Annotating Pisgah Systematically</>
              </div>

              <div class="navicon">
                <div class="menu-container">

                  <div class="circle dark inline">
                    <i class="icon ion-navicon"></i>
                  </div>

                  <div class="list-menu">
                    <i class="icon ion-close-round close-iframe"></i>
                    <div class="intro-inner">
                     	<ul id="nav-menu">
                     	 <li><a href="index.php">PMAPS</a></li>
                       	 <li><a href="about.html">About</a></li>
                       	 <li><button class="headerButton" onclick='startComment()'>Submit A Comment</button></li>
                       	 <li><a href="contact.html">Contact</a></li>
                      </ul>
                    </div>
                  </div>

                </div>
              </div>

            </div>
         </div>
      </div>

   </nav>
</div>
<div class="filter">
	<table>
		<tr>
			<th>Name</th><th>Type</th><th>Trip</th>
		</tr>
		<tr>
			<td>
				<input type="text" id="nameCheck" />
			</td>
			<td>
				<input type="checkbox" id="generalCheck" checked> General<br/></input>
				<input type="checkbox" id="vamCheck" checked> VAM<br/></input>
				<input type="checkbox" id="waterCheck" checked> Water<br/></input>
				<input type="checkbox" id="safetyCheck" checked> Safety<br/></input>
				<input type="checkbox" id="campsiteCheck" checked> Campsite<br/></input>
				<input type="checkbox" id="tipCheck" checked> Tip<br/></input>
				<input type="checkbox" id="solosCheck" checked> Solos<br/></input>
			</td>
			<td>
				<input type="checkbox" id="augCheck" checked> August<br/></input>
				<input type="checkbox" id="marchCheck" checked> March<br/></input>
				<input type="checkbox" id="stepCheck" checked> STEP<br/></input>
			</td>
		</tr>
		<tr>
			<td colspan="3">Minimum Date: <input type="date" id="minDateCheck" /><br/>
				Maximum Date: <input type="date" id="maxDateCheck" /></td>
		</tr>
		<tr>
			<td>
				<button onclick="filterCommentsFromForm()">Submit</button>
			</td>
			<td colspan="2">
				<button onclick="resetSelection()">Reset Selection</button>
			</td>
		</tr>
	</table>
</div>
<div class="body" id="commentList"></div>
<div id="footer">Some of these maps generated with <a href="http://www.maptiler.com/">MapTiler</a></div>
<div id="map"></div>
</body>
</html>
