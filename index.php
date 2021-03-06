<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if ($auth->isLogged()) {
	header("Location: home.php");
	die('You are logged in');
}

?>
<html>
<head>
	<meta charset='utf-8' />
	<title>sylvstrr</title>
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
	<script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
	<link rel="icon" type="image/png" href="./assets/favicon.png">
</head>

<body>
	<main role="main" class="container-fluid no-gutters">
		<div class="row">
			<div id="map">
			</div>
			<div class="col"></div>
			<div class="col text-center no-gutters header">
				<h1>sylvstrr</h1>
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
		<!-- Search form -->
		<!-- <form action="search.php" method="POST">
			<div class="row">
				<div class="col-6"></div>
				<div class="col-6">
					<div class="row">
						<div class="col-md-12 col-lg-6 input-effect">
							<div class="md-form">
								<input type="text" autocomplete="off" id="keyword" name="keyword" class="form-control">
								<label for="keyword" class="float-up keyword-label">Keywords</label>
								<span class="help d-none">Separate keywords with a space</span>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 input-effect">
							<div class="md-form">
								<input type="text" autocomplete="off" class="form-control" name="location" id="location">
								<label for="location" class="float-up location-label">Location</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br><br>
			<div class="row">
				<div class="col"></div>
				<div class="col text-center">
					<input type="submit" class="btn btn-success submit" data-toggle="button" aria-pressed="false" autocomplete="off">
				</div>
			</div>
		</form> -->
		<!-- Login form -->
		<form>
			<div class="row">
				<div class="col-6"></div>
				<div class="col-6">
					<div class="row">
						<div class="col-md-12 col-lg-6 input-effect">
							<div class="md-form">
								<input type="text" autocomplete="off" id="email" name="email" class="form-control">
								<label for="email" class="float-up">Email</label>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 input-effect">
							<div class="md-form">
								<input type="password" autocomplete="off" class="form-control" name="password" id="password">
								<label for="password" class="float-up">Password</label>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 input-effect 2fa" style="display: none">
							<div class="md-form">
								<input type="text" autocomplete="off" class="form-control" name="code" id="code">
								<label for="code" class="float-up">Code</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br><br>
			<div class="row">
				<div class="col"></div>
				<div class="col text-center">
					<input type="submit" class="btn btn-success submit" data-toggle="button" aria-pressed="false" autocomplete="off" value="Login">
				</div>
			</div>
		</form>
		<div class="row">
			<div class="col"></div>
			<div class="col text-center">
				<a href="signup">Need an account?</a>
			</div>
		</div>
		<br><br>
		<!-- <div class="row">
			<div class="col"></div>
			<div class="col text-right" style="position: absolute; bottom: 10;">
				<a href="trainer/trainer.php">
					<button type="button" class="btn btn-primary">Train me</button>
				</a>
			</div>
		</div> -->
	</main>
	<script>
	function getRandomInRange(from, to, fixed) {
		return (Math.random() * (to - from) + from).toFixed(fixed) * 1;
	}

	mapboxgl.accessToken = 'pk.eyJ1IjoibWFzb25wYXdzZXkiLCJhIjoiY2puemkzb3N0MWY4djNra2JsZzBpaXpicSJ9.O8dFlt7FrskfE-GL8qvBUA';
	var zoom = Math.floor(Math.random() * 3) + 4;
	var lng = getRandomInRange(-90, -20, 5);
	var lat = getRandomInRange(20, 90, 5);
	var map = new mapboxgl.Map({
		container: 'map', // container id
		style: 'mapbox://styles/masonpawsey/cjnzi73pd85jn2rmnm1oj9g65', // stylesheet location
		center: [lng, lat], // starting position [lng, lat]
		zoom: zoom,
		interactive: false
	});

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
		// playback(0);
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
			var formdata = $("form").serialize();
			$.ajax({
				url: 'login.php',
				type: 'POST',
				data: formdata,
				dataType: "json",
				success: function (result) {
					$('.submit').removeClass('active');
					if(result['error'] === true) {
						// We supply a title in our error messages but PHPAuth doesn't
						// so, if `result['title']` doesn't exist, just print 'Error'
						toastr.error(result['message'], result['title'] || 'Error');
					} else if(result['2fa'] === true) {
						// If we find out that the user has 2FA enabled, display the field
						// for them to enter the code
						toastr.success('A code has been sent to (xxx) xxx-xx' + result['phone'], 'Code sent!');
						$("#code").inputmask({"mask": "9999", showMaskOnHover: false});
						$('.2fa').show();
						$('#code').focus();
					} else {
						window.location.href = "home";
					}
				}
			});
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
