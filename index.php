<?php
session_start();
$_SESSION['id'] = uniqid();
$path = 'tweets/'.$_SESSION['id'];

// if (!is_dir($path)) {
//     mkdir($path, 0777, true);
// }
?>
<html>
<head>
	<meta charset='utf-8' />
	<title>Display a map</title>
	<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.css' rel='stylesheet' />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/css/mdb.min.css" rel="stylesheet">
	<script
	  src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
	  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
	  crossorigin="anonymous"></script>
	<!-- MDB core JavaScript -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/js/mdb.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<script type="text/javascript" src="cities.js"></script>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<div class="container-fluid no-gutters">
		<div class="row">
			<div id="map">
			</div>
			<div class="col"></div>
			<div class="col text-center no-gutters header">
				<h1>sylvester</h1>
			</div>
		</div>
		<div class="row">
			<div class="col"></div>
			<div class="col header-text">
				<div class="row">
					<div class="col-md-12 col-lg-3"></div>
					<div class="col-md-12 col-lg-6 header-text">
						sentiment analysis of <span style="color:#0084b4">tweets</span>, based on <span style="color:#EE6352">keywords</span> for specific <span style="color:#4CB944">geographic areas</span>
					</div>
					<div class="col-md-12 col-lg-3"></div>
				</div>
			</div>
		</div>
		<br><br><br>
		<form action="search.php" method="POST">
			<div class="row">
				<div class="col"></div>
				<div class="col-3 input-effect">
					<div class="md-form">
						<input type="text" autocomplete="off" id="keyword" name="keyword" class="form-control">
						<label for="keyword" class="float-up keyword-label">Keywords</label>
						<span class="help d-none">Separate keywords with a space</span>
					</div>
				</div>
				<div class="col-3 input-effect">
					<div class="md-form">
				    	<input type="text" autocomplete="off" class="form-control" name="location" id="location">
				    	<label for="location" class="float-up location-label">Location</label>
				    </div>
			    </div>
			</div>
			<br><br>
			<div class="row">
				<div class="col"></div>
				<div class="col text-center">
					<!-- <input type="submit" name="submit" value="Submit"/> -->
					<input type="submit" class="btn btn-primary submit" data-toggle="button" aria-pressed="false" autocomplete="off">
				</div>
			</div>
		</form>
	</div>
	<script>
	mapboxgl.accessToken = 'pk.eyJ1IjoibWFzb25wYXdzZXkiLCJhIjoiY2puemkzb3N0MWY4djNra2JsZzBpaXpicSJ9.O8dFlt7FrskfE-GL8qvBUA';
	var map = new mapboxgl.Map({
		container: 'map', // container id
		style: 'mapbox://styles/masonpawsey/cjnzi73pd85jn2rmnm1oj9g65', // stylesheet location
		center: [-122.43512,37.761], // starting position [lng, lat]
		zoom: 17,
		interactive: false
	});

	var map_locations = [{
		// CSUB
	    "id": "2",
	    "camera": {
	    	speed: 0.1,
	        center: [-119.10506171751422,35.347574509536656],
	        zoom: 14.75,
	        pitch: 50
	    }
	}, {
		// White House
	    "id": "3",
	    "camera": {
	    	speed: 0.1,
	        center: [-77.03657390000001, 38.8976633],
	        zoom: 17
	    }
	}, {
		// Kremlin
	    "id": "1",
	    "camera": {
	    	speed: 0.1,
	        center: [37.61749940000004,55.7520233],
	        zoom: 16,
	        pitch: 25
	    }
	}, {
		// Trump Tower
	    "id": "4",
	    "camera": {
	    	speed: 0.1,
	        center: [-73.973869,40.762459], // starting position [lng, lat]
	        zoom: 17
	    }
	}, {
		// Buckingham Palace
	    "id": "5",
	    "camera": {
	    	speed: 0.1,
	    	center: [-0.140634, 51.501476], // starting position [lng, lat]
	    	zoom: 15,
	    	pitch: 75,
	    	yaw: 25,
	    }
	}, {
		// mar - a - lago
		"id": "6",
	    "camera": {
	    	speed: 0.1,
	        center: [-80.037980,26.676820], // starting position [lng, lat]
	        zoom: 17
	    }
	 }, {
	 	// the castro
		"id": "7",
	    "camera": {
	    	speed: 0.1,
	        center: [-122.43512,37.761], // starting position [lng, lat]
	        zoom: 17,
	    }
	}, {
	 	// the bean
		"id": "8",
	    "camera": {
	    	speed: 0.1,
	        center: [-87.620659184,41.8762748282], // starting position [lng, lat]
	        zoom: 17,
	    }
	}];

	function playback(index) {
	    // Animate the map position based on camera properties
	    map.flyTo(map_locations[index].camera);

	    map.once('moveend', function() {
	        // Duration the slide is on screen after interaction
	        window.setTimeout(function() {
	            // Increment index
	            index = (index + 1 === map_locations.length) ? 0 : index + 1;
	            playback(index);
	        }, 3000); // After callback, show the location for 3 seconds.
	    });
	}

	map.on('load', function() {
	    // Start the playback animation for each borough
	    playback(0);
	});



	$(document).ready(function() {
		$('input').focus(function() {
			$(this).next('.float-up').addClass('active');
		});

		$('input').focusout(function() {
			if ($(this).val().length < 1) {
				$(this).next('.float-up').removeClass('active');
			}
		});


		// Gonna need to validate these some more...
		$('.submit').on('click', function() {
			if( $('#keyword').val().trim().length < 1 || $('#keyword').val().trim().length < 1) {
				toastr.error('Please supply a keyword and a location', 'Error');
			} else {
				$("form").submit();
			}
		});

		$('#keyword').on('focus', function() {
			$('.help').removeClass("d-none");
			$('.help').addClass("d-block");
		});

		$('#keyword').on('blur', function() {
			$('.help').removeClass("d-block");
			$('.help').addClass("d-none");
		});

		// Cities are included in cities.js above
		$("#location").autocomplete({
			source: cities
		});
	});
	</script>
</body>

</html>