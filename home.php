<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
	header("Location: index");
	die('Forbidden');
}

// Add user action to log
$statement = $dbh->prepare('INSERT INTO user_log (uid, ip, agent, `time`, action) VALUES (:uid, :ip, :agent, NOW(), :action)');
$statement->execute([
	'uid' => $auth->getCurrentUser()['uid'],
	'ip' => $_SERVER['REMOTE_ADDR'],
	'agent' => $_SERVER['HTTP_USER_AGENT']??null,
	'action' => 'home'
]);
// Print errors, if they exist
if($statement->errorInfo()[0] != "00000") {
	print_r($statement->errorInfo());
	die();
}

// Clear the 2fa verify_code upon successful login
// If it doens't exist, it'll just stay NULL
$statement = $dbh->prepare('UPDATE phpauth_users SET verify_code = NULL WHERE id = :id');
$statement->execute([
	'id' => $auth->getCurrentUser()['uid']
]);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>sylvstrr</title>
	<!-- Boostrap CSS & JS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/js/mdb.min.js"></script>
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.css' rel='stylesheet' />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/css/mdb.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="home-style.css">
	<script type="text/javascript" src="cities.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/downloadjs/1.4.8/download.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<link rel="icon" type="image/png" href="./assets/favicon.png">
	<script>
	window.paceOptions = {
		ajax: {
			trackMethods: ['GET', 'POST', 'PUT', 'DELETE', 'REMOVE']
		},
		startOnPageLoad: false
	};
	</script>
	<style type="text/css">
		.pace {
		  -webkit-pointer-events: none;
		  pointer-events: none;

		  -webkit-user-select: none;
		  -moz-user-select: none;
		  user-select: none;
		}

		.pace-inactive {
		  display: none;
		}

		.pace .pace-progress {
		  background: #4285F4;
		  position: fixed;
		  z-index: 2000;
		  top: 0;
		  right: 100%;
		  width: 100%;
		  height: 5px;
		}
	</style>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
	<script src="vkbeautify.js"></script>
</head>

<body>
	<div id="wrapper">
		<!-- Sidebar -->
		<div id="sidebar-wrapper">
			<ul class="sidebar-nav">
				<li class="sidebar-brand">
					<a href="#">

					</a>
				</li>
				<li>
					<a href="home">Dashboard</a>
				</li>
				<li>
					<a href="history">Query history</a>
				</li>
				<li>
					<a href="gallery">Gallery</a>
				</li>
				<li>
					<a href="profile">Profile</a>
				</li>
			</ul>
		</div>
		<!-- /#sidebar-wrapper -->
		<!-- Page Content -->
		<div id="page-content-wrapper">
			<header>
				<!-- Fixed navbar -->
				<nav class="navbar navbar-expand fixed-top">
					<a class="navbar-brand" href="#">sylvstrr</a>
					<div class="collapse navbar-collapse" id="navbarCollapse">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item">
								<a class="nav-link" href="#menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></a>
							</li>
						</ul>
					</div>
					<img class="rounded-circle" style="width:35px; margin: 0 10px;" src="https://media.licdn.com/dms/image/C5603AQEAmCS6ZYjupg/profile-displayphoto-shrink_800_800/0?e=1550102400&v=beta&t=U6TrOrwZ6hFBTgAwqzFDpk6aBkSKi_ZqsdM-twcWWRU" alt="">
				  <li class="nav-item dropdown" style="list-style: none;">
						  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo $auth->getCurrentUser()['email']; ?>
						  </a>
						  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="profile">Profile</a>
							<a class="dropdown-item" href="logout">Log out</a>
						  </div>
						</li>
				</nav>
			</header>
			<!-- Begin page content -->
			<main role="main" class="container-fluid">
				<div class="row" style="margin-top: 60px">
					<div class="col-md-8">
						<div class="card">
							<div class="card-body">
								<form id="search_form">
									<h5 class="card-title"><strong>New search</strong></h5>
									<div class="row">
										<div class="col-md-12 col-lg-5 input-effect">
											<div class="md-form">
												<input type="text" autocomplete="off" id="keyword" name="keyword" class="form-control">
												<label for="keyword" class="float-up keyword-label">Keywords</label>
												<span class="help d-none">Separate keywords with a space</span>
											</div>
										</div>
										<div class="col-md-12 col-lg-5 input-effect">
											<div class="md-form">
												<input type="text" autocomplete="off" class="form-control" name="location" id="location">
												<label for="location" class="float-up location-label">Location</label>
											</div>
										</div>
										<div class="col-md-12 col-lg-2">
											<div class="md-form">
												<button type="submit" class="btn btn-hollow btn-100">submit</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br>
						<div class="card">
							<div class="card-body">
								<div class="row">
								  <div class="col">
									<h5 class="card-title map-title"></h5>
								  </div>
								  <div class="col text-right map-options d-none">
									<a href="#" class="download"><i class="fas fa-arrow-circle-down"></i> Download</a>
									<a href="#" class="save"><i class="fas fa-cloud-download-alt"></i> Save</a>
								  </div>
								</div>
							  <div id="map" style="height: 58vh"></div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title"><strong>Trending on Twitter</strong></h5>
								<p class="card-text">Click on one of these hot hashtags that are trending worldwide right now</p>
								<p class="card-text">
								  <ul>
									<?php
									$url = "https://tagdef.com/en/";
									$ch = curl_init();
									$timeout = 5;
									curl_setopt($ch, CURLOPT_URL, $url);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
									curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
									$html = curl_exec($ch);
									curl_close($ch);

									// Create a DOM parser object
									$dom = new DOMDocument();
									@$dom->loadHTML($html);

									// Iterate over all the <ul> tags
									$tags = $dom->getElementsByTagName('ul');
									$array = explode('#',$tags[4]->nodeValue);
									foreach (array_filter($array) as $key => $value) {
										echo "<li class='trending_topic'>#" . $value . "</li>";
									}
									?>
								  </ul>
								</p>
							</div>
						</div>
						<br>
						<div class="card">
							<div class="card-body">
								<h5 class="card-title"><strong>Your recent searches</strong></h5>
								<div class="card-text recent-searches">
									<?php
									// Load this first from the server, then let search.php take over to keep it updated
									$statement = $dbh->prepare('SELECT `time`, keyword, location FROM queries WHERE uid = :uid ORDER BY `time` DESC LIMIT 3');
									$statement->execute([
										'uid' => $auth->getCurrentUser()['uid']
									]);
									// Print errors, if they exist
									if($statement->errorInfo()[0] != "00000") {
										print_r($statement->errorInfo());
										die();
									} else {
										$most_recent_queries = $statement->fetchAll(PDO::FETCH_ASSOC);
										if(empty($most_recent_queries)) {
											echo "<p class='card-text'>We'll keep track of your query history here. Make a query and watch the magic!</p>";
										} else {
											foreach ($most_recent_queries as $key => $value) {
												?>
												<div class="row">
												  <div class="col-8">
													<i class="w-20-px fas fa-keyboard"></i> <?php echo $value['keyword']; ?> <br><i class="w-20-px fas fa-map-marker"></i> <?php echo $value['location']; ?><br><i class="w-20-px fas fa-clock"></i> <?php echo $value['time']; ?>
												  </div>
												  <div class="col-4">
													<button type="button" class="btn btn-hollow btn-100" data-toggle="button" aria-pressed="false" autocomplete="off">View</button>
												  </div>
												</div>
												<?php
												// Don't print a break on the last row
												if($key+1 != count($most_recent_queries)) {
													echo "<br>";
												}
											}
										}
									}
									?>
								</div>
							</div>
							<div class="card-footer recent-searches-card-footer text-right <?php if(empty($most_recent_queries)) { echo "d-none"; } ?>">
							  <a href="history"><small class="text-muted">See more <i class="fas fa-arrow-circle-right"></i></small></a>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-12">
										<h5 class="card-title debug"><strong>Debug</strong></h5>
										<pre id="debug"></pre>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-12">
										<h5 class="card-title xml"><strong>XML</strong></h5>
										<pre id="xml" lang="xml"></pre>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<!-- /#page-content-wrapper -->
		<!-- /#wrapper -->
		<footer class="footer">
			<div class="container-fluid">
				<div class="row">
				  <div class="col-6"><a href='https://www.mapbox.com/about/maps/' target='_blank'>Maps &copy; Mapbox &copy; OpenStreetMap</a></div>
				  <div class="col-6 text-right">sylvstrr &copy; <?php echo date('Y'); ?></div>
				</div>
			</div>
		</footer>
	</div>
	<script>
	mapboxgl.accessToken = 'pk.eyJ1IjoibWFzb25wYXdzZXkiLCJhIjoiY2puemkzb3N0MWY4djNra2JsZzBpaXpicSJ9.O8dFlt7FrskfE-GL8qvBUA';
	var map = new mapboxgl.Map({
		container: 'map', // container id
		style: 'mapbox://styles/masonpawsey/cjnzi73pd85jn2rmnm1oj9g65', // stylesheet location
		center: [-119.10506171751422, 35.34757450953665], // starting position [lng, lat]
		zoom: 4,
		interactive: true,
		preserveDrawingBuffer: true
	});

	function downloadInnerHtml(filename, elId, mimeType) {
		// Replace &lt; &gt; and &nbsp; with XML friendly chars
		var elHtml = document.getElementById(elId).innerHTML.replace(/\&lt;/g, '<').replace(/\&gt;/g, '>').replace(/\&nbsp;/g, '&#160;');
		var link = document.createElement('a');
		mimeType = mimeType || 'text/plain';

		link.setAttribute('download', filename);
		link.setAttribute('href', 'data:' + mimeType + ';charset=utf-8,' + encodeURIComponent(elHtml));
		link.click(); 
	}

	function getColour(sentiment) {
		if(sentiment >= 0 && sentiment < 0.4) {
			return '#FF6666';
		}
		if(sentiment >= 0.4 && sentiment < 0.45) {
			return '#FFFF66';
		}
		if(sentiment >= 0.45 && sentiment < 0.55) {
			return '#B3FF66';
		}
		if(sentiment >= 0.55 && sentiment < 1) {
			return '#67FF66';
		}
	}

	function urltoFile(url, filename, mimeType){
		mimeType = mimeType || (url.match(/^data:([^;]+);/)||'')[1];
		return (fetch(url)
			.then(function(res){return res.arrayBuffer();})
			.then(function(buf){return new File([buf], filename, {type:mimeType});})
		);
	}

	$(document).ready(function() {
		var map_id = 0;
		var last_inserted = 0;

		$('input').focus(function() {
			$(this).next('.float-up').addClass('active');
		});

		$('input').focusout(function() {
			if ($(this).val().length < 1) {
				$(this).next('.float-up').removeClass('active');
			}
		});

		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
			$('.navbar-collapse').toggleClass("padded");
		});

		$('#keyword').on('focus', function() {
			$('.help').removeClass("d-none");
			$('.help').addClass("d-block");
		});

		$('#keyword').on('blur', function() {
			$('.help').removeClass("d-block");
			$('.help').addClass("d-none");
		});

		$('.download').on('click', function() {
			var map_code = map.getCanvas().toDataURL();
			download(map_code, "map.png", "image/png");
		});

		$('.save').on('click', function() {
			var map_code = map.getCanvas().toDataURL();
			$.ajax({
				url: 'uploadimage.php',
				type: 'POST',
				data: {
					'map_code': map.getCanvas().toDataURL(),
					'last_inserted': last_inserted
				},
				success: function(data) {
					toastr.success('You image has been saved to your gallery', 'Saved!');
				},
				error: function(request,error) {
					console.error("Request: "+JSON.stringify(request));
				}
			});
		});

		$('#search_form').on('submit', function(e) {
			e.preventDefault();
			var keyword = $('#keyword').val();
			var location = $('#location').val();
			$('.download-xml').remove();
			$('.download-json').remove();
			$.ajax({
				url: 'search.php',
				type: 'POST',
				data: {
					'keyword' : keyword,
					'location' : location
				},
				success: function(data) {
					var sentiment = parseFloat(JSON.parse(data)[4]);
					last_inserted = JSON.parse(data)[5];
					$('.xml').append('<button class="btn btn-hollow download-xml float-right">Download XML</button>');
					$('.debug').append('<button class="btn btn-hollow download-json float-right">Download JSON</button>');
					$('#debug').html(JSON.parse(data)[0]);
					$('#xml').text(vkbeautify.xml(JSON.parse(data)[3]));
					$('.map-title').html('<strong>Map for <u>'+keyword+'</u> in <u>' +location+ '</u><small style="margin-left:25px">Score: '+sentiment.toFixed(2)+'<small></strong>');
					$('.map-options').removeClass('d-none');
					$('.recent-searches-card-footer').removeClass('d-none');
					$('#keyword').val('').blur();
					$('#location').val('').blur();
					var recent_searches_html = '';
					JSON.parse(data)[2].forEach(function(item, i) {
						recent_searches_html += `<div class="row">
						  <div class="col-8">
							<i class="w-20-px fas fa-keyboard"></i> ` + item['keyword'] + ` <br><i class="w-20-px fas fa-map-marker"></i> ` + item['location'] + `<br><i class="w-20-px fas fa-clock"></i> ` + item['time'] + `
						  </div>
						  <div class="col-4">
							<button type="button" class="btn btn-hollow btn-100" data-toggle="button" aria-pressed="false" autocomplete="off">View</button>
						  </div>
						</div>`
						// Don't print a break on the last row
						if(i+1 != JSON.parse(data)[2].length) {
							recent_searches_html += "<br>";
						}
					});
					$('.recent-searches').html(recent_searches_html);
					

					const metersToPixelsAtMaxZoom = (meters, latitude) => meters / 0.075 / Math.cos(latitude * Math.PI / 180)

					var dpi = 300;
					Object.defineProperty(window, 'devicePixelRatio', {
						get: function() {return dpi / 96}
					});

					map.addSource('source_' + map_id, {
							"type": "geojson",
							"data": {
								"type": "FeatureCollection",
								"features": [{
									"type": "Feature",
								"geometry": {
									"type": "Point",
									"coordinates": [JSON.parse(data)[1][0], JSON.parse(data)[1][1]]
								}
							  }]
							}
						  });

						map.addLayer({
							"id": map_id + '',
							"type": "circle",
							"source": 'source_' + map_id,
							"paint": {
								"circle-radius": {
									stops: [
										[0, 0],
										[20, metersToPixelsAtMaxZoom(50000, JSON.parse(data)[1][1])]
									],
									base: 2
								},
								"circle-color": getColour(sentiment),
								"circle-opacity": 0.3
							}
						})

						map.flyTo({
							center: {lng: JSON.parse(data)[1][0], lat: JSON.parse(data)[1][1]},
							zoom: 7
						});

						// Credit: https://stackoverflow.com/a/37794326
						map_id++;

				},
				error: function(request,error)
				{
					console.error("Request: "+JSON.stringify(request));
				}
			});
		})

		$('.trending_topic').on('click', function() {
			$('#keyword').focus();
			$('#keyword').val($(this).text());
			$('#location').focus();
		})

		// Cities are included in cities.js above
		$("#location").autocomplete({
			source: cities
		});

	});

	$(document).on('click','.download-xml', function(){
		downloadInnerHtml('corpus.xml', 'xml','text/html');
	});

	$(document).on('click','.download-json', function(){
		downloadInnerHtml('corpus.json', 'debug','text/json');
	});
	</script>
</body>

</html>
