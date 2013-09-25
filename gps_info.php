<!DOCTYPE html>
<html>
	<head>
		<title>Image GPSLocation information Test</title>
		<meta content="text/html;charset=UTF-8" http-equiv="content-type"/>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script> <!-- Base jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script> <!-- jQuery UI -->
		<script src="http://maps.google.com/maps/api/js?sensor=false"></script> <!-- For getting Google Maps -->
		<script src="gps_js/gps.min.js"></script> <!-- Base GPS functions -->
		<script src="gps_js/jquery.filedrop.js"></script>
	    <script src="gps_js/script.js"></script>
	    <script src="gps_js/jquery.exif.js"></script>
		<script type="text/javascript">
			/* File listing */

			$(document).ready(function(){

				/*
				$.ajax({
					type: "GET",
					url:"gps_image_list.php",
					timeout: 3000,
					success:function(result){
				    	img_result = $("#image_list").html(result);
				    }
				});
				*/
				
				$('.img').live("click", function(){
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
					     console.log($(this).exifPretty());
					    
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
					    
					    var $tabs = $('#tabs');
					    
					    if($tabs.tabs('option', 'selected') == '0') {
							$tabs.tabs('select', 2);
					    }
				}); // End IMG.click
					
				
				$(function() {
					$( "#tabs" ).tabs();
					$('#tabs').bind('tabsshow', function(event, ui) {
					    if (ui.panel.id == "tabs-3") {
					    	initializeDirections(LatDecimalDegrees, LonDecimalDegrees);
					    }
					    if (ui.panel.id == "tabs-2") {
					    	initialize(LatDecimalDegrees, LonDecimalDegrees);
					    }
					});
				});
				
			});
		</script>
		
		<!-- CSS Declarations -->
		<link rel="stylesheet" href="./css/gps.css" type="text/css" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/dark-hive/jquery-ui.css" type="text/css" />
		<link rel="stylesheet" href="./css/styles.css" />
	</head>
	
	<body>
	
		<div id="wrapper">
			<header>
				<a href="/test_bed/gps_info.html"><img id="logo" src="/images/photoNav_01.png" /></a>
				<div id="status"></div>
			</header>

			<section>
				<article id="image_list">
					<?php
					if ($handle = opendir('./imgUpload')) {
					    while (false !== ($entry = readdir($handle))) {
					        if ($entry != "." && $entry != "..") {
					        	?>
					        	<div class="img_container">
						        	<img src="/test_bed/imgUpload/<?php echo $entry; ?>" class="img" exif="true" />
						        </div>
					        	<?php
					        }
					    }
					    closedir($handle);
					}
					?>
				</article>
			</section>
			
			<br style="clear: both;" />
			
			<section>			
				<article>
					<div id="tabs">
						<ul>
							<li><a href="#tabs-1">Overview</a></li>
							<li><a href="#tabs-2">Map Overview</a></li>
							<li><a href="#tabs-3">Driving Directions</a></li>
							<li><a href="#tabs-4">Textural Route</a></li>
						</ul>
						<div id="tabs-1">
							<p><strong>Overview:</strong> This example is to get GPS data stored from images to be able to return to
							the location that the image was originally taken from based on your current location. Other great uses 
							would be for your friends to be able to visit these locations such as parks, landmarks or points of interest.</p> 
							<br />
							<p><strong class="alert">DIRECTIONS/NOTES:</strong> <strong>You will need to make sure that Geoloation for images is ENABLED before uploading taking pictures.</strong> </p>
						</div>
						<div id="tabs-2">
							<!-- Placeholder for Google Maps -->
							<div id="map_canvas"></div>
						</div>
						<div id="tabs-3">
							<div id="map_canvas2"></div>
						</div>
						<div id="tabs-4">	
							<div id="route"></div>
						</div>
					</div>
						<div id="current_location">
							<h3>Current Coordinates</h3>
							<span id="latitude"></span>, <span id="longitude"></span> within: <span id="accuracy"></span> meters.						
						</div>
					
				</article>
			</section>
			
			<br style="clear: both;" />
			
			<section>
				<article>
					<form id="submitGPSImage" action="fileUploadProcess.php" method="post" enctype="multipart/form-data">
						<label for="gpsImage">Filename:</label>
						<input type="file" name="gpsImage" id="gpsImage" /> 
						<input id="submit" type="submit" name="submit" value="submit" />
					</form>
				</article>
			</section>
			
			<br style="clear: both;" />
			
			<section>
				<article>

					<div id="dropbox">
						<span class="message">Drop images here to upload. <br /><i>(they will only be visible to you)</i></span>
					</div>

					<div class="message"></div>
				</article>
			</section>

		</div>
		
		<footer>
			<h3>Image GPS Extraction</h3>
			<h5>
				Written by: Peter Robie<br/>
				March, 2012
			</h5>
			<span id="status"></span>
		</footer>
	</body>
	
	<script type="text/javascript">loadGeoData();</script>
</html>
