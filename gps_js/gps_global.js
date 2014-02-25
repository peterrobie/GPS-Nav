// GeoLocation to Image creation location.

var LatDecimalDegrees;
var LonDecimalDegrees;

function getImageGPSData() {
		
	var longitude = $(this).exif("GPSLongitude");
	var latitude = $(this).exif("GPSLatitude");
	
	var latRef = $(this).exif("GPSLatitudeRef");
	var lngRef = $(this).exif("GPSLongitudeRef");

	// Let's make the object to a string to split on the ,'s
	var a1 = new Array();
	var a2 = new Array();
	
	a1=longitude.toString().split(',');
	a2=latitude.toString().split(',');
	
	if(lngRef == "W") {
		a1[0] = Number(a1[0]);
		a1[0] = -a1[0];
	}
    
    if(latRef == "S") {
		a2[0] = Number(a2[0]);
		a2[0] = -a2[0];
	}
    
    // Log all of out exif data
    // console.log($(this).exifPretty());
    
    /* Populate the fields with the correct data */
    // Latitude
    var LatDegrees = parseInt(a2[0]);
    var LatMinutes = parseInt(a2[1]);
    var LatSeconds = parseInt(a2[2]);
    
    // Longitude
    var LonDegrees = parseInt(a1[0]);
    var LonMinutes = parseInt(a1[1]);
    var LonSeconds = parseInt(a1[2]);
    
    toDecimal(LatDegrees, LatMinutes, LatSeconds, LonDegrees, LonMinutes, LonSeconds);
}

// DMS Conversion Functions 
function convert(D,M,S){
     var DD;
     D < 0 ? DD = roundOff(D + (M/-60) + (S/-3600),6) : DD = roundOff(D + (M/60) + (S/3600),6);
     return DD;
}

function roundOff(num,decimalplaces){
     var decimalfactor = Math.pow(10,decimalplaces);
     var roundedValue = Math.round(num*decimalfactor)/decimalfactor;
     return roundedValue;
}

function toDecimal(LatDegrees, LatMinutes, LatSeconds, LonDegrees, LonMinutes, LonSeconds){			 
     LatDecimalDegrees = convert(LatDegrees,LatMinutes,LatSeconds);
     LonDecimalDegrees = convert(LonDegrees,LonMinutes,LonSeconds);
     
     // Pass parsed data to the GoogleMaps API
     initialize(LatDecimalDegrees, LonDecimalDegrees);
}

//////////////////////////////////////////////
// GOOGLE MAPS CODE
//////////////////////////////////////////////

// GEOLocation functions
var altitudeAccuracy;
var accuracy; 
var altitude;

// Maps for directions
var map;
var directionsPanel;
var directions;
var geoLatitude;
var geoLongitude;

//Google Maps Init
function initialize(LatDecimalDegrees, LonDecimalDegrees) {
	var myOptions = {
		center: new google.maps.LatLng(LatDecimalDegrees, LonDecimalDegrees),
		zoom: 15,
		mapTypeId: google.maps.MapTypeId.SATELLITE
	};

	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	// Creating a marker and positioning it on the map
	var marker = new google.maps.Marker({
	  position: new google.maps.LatLng(LatDecimalDegrees, LonDecimalDegrees),
	  map: map
	});
	
	// initialize directions
	initializeDirections(LatDecimalDegrees, LonDecimalDegrees);
	
	return LatDecimalDegrees, LonDecimalDegrees;
}

function loadGeoData() 
{
    if(navigator.geolocation) 
	{
        navigator.geolocation.getCurrentPosition(updateLocation);
    }
}

function updateLocation(position) {
	geoLatitude = position.coords.latitude;
	geoLatitude = geoLatitude.toFixed(3)
	geoLongitude = position.coords.longitude;
	geoLongitude = geoLongitude.toFixed(3);
	geoAccuracy = position.coords.accuracy;
	altitude = position.coords.altitude;
		
    if (!geoLatitude || !geoLongitude) {
        document.getElementById("status").innerHTML = "HTML5 Geolocation is supported in your browser, but location is currently not available.";
        return;
    }

    document.getElementById("latitude").innerHTML = geoLatitude;
    document.getElementById("longitude").innerHTML = geoLongitude;
    document.getElementById("accuracy").innerHTML = geoAccuracy;
    return geoLatitude, geoLongitude;
    
}



/////////////////////////////////////////////////////////////////
// Google Maps Directions
/////////////////////////////////////////////////////////////////

var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var stepDisplay;
var markerArray = [];

// Static global defines
var haight;
var oceanBeach;

function initializeDirections(LatDecimalDegrees, LonDecimalDegrees) {
	console.log('GeoLatitude: ' + geoLatitude + '\nGeoLongit' + geoLongitude);
	
	//haight = new google.maps.LatLng(32.9305604, -96.92159339999999);
	haight = new google.maps.LatLng(geoLatitude, geoLongitude);
	oceanBeach = new google.maps.LatLng(LatDecimalDegrees, LonDecimalDegrees);
	  
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myOptions = {
		zoom: 14,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		//center: directionsStart
		center: haight
	}
	map = new google.maps.Map(document.getElementById("map_canvas2"), myOptions);
	directionsDisplay.setMap(map);
	//Instantiate an info window to hold step text.
	stepDisplay = new google.maps.InfoWindow();
	  
	calcRoute(LatDecimalDegrees, LonDecimalDegrees);
}

// Add this to the route calculation

function calcRoute() {
  //var selectedMode = document.getElementById("mode").value;
  var request = {
	origin: haight,
	destination: oceanBeach,
	travelMode: google.maps.TravelMode.DRIVING
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
      showSteps(response); 
    }
  })
}

function attachInstructionText(marker, text) {
	// set the instructions to a marker for each step
	google.maps.event.addListener(marker, 'click', function() {
		stepDisplay.setContent(text);
		stepDisplay.open(map, marker);
	});
}

function infoWindowResults(marker, text) {
	var infoWindow = document.getElementById('route');
	
	for(i=0;i<myRoute.steps.length;i++){
		infoWindow.innerHTML = i + ": " + text;
	}
}

function showSteps(directionResult) {
	// For each step, place a marker, and add the text to the marker's
	// info window. Also attach the marker to an array so we
	// can keep track of it and remove it when calculating new
	// routes.
	var myRoute = directionResult.routes[0].legs[0];
	var infoWindow = document.getElementById('route');
	var j = 1;
	
	$('#route').empty();
	
	for (var i = 0; i < myRoute.steps.length; i++) {
		var marker = new google.maps.Marker({
			position: myRoute.steps[i].start_point,
			map: map
		});
		attachInstructionText(marker, myRoute.steps[i].instructions);
		markerArray[i] = marker;
		      
		$('#route').append( (j++) + ': ' + myRoute.steps[i].instructions + '<p/>');
	} 
	
}