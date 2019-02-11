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
	'action' => 'gallery'
]);
// Print errors, if they exist
if($statement->errorInfo()[0] != "00000") {
	print_r($statement->errorInfo());
	die();
}

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

		.map-image {
			padding: 25px;
		}

		.map-image .card {
			box-shadow: none;
		}

		.map-image .card-body {
			padding: 1.25rem 1.25rem 1.25rem 0;
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
					<a class="navbar-brand" href="home">sylvstrr</a>
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
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<?php
									// Get the users searches!
									$statement = $dbh->prepare("SELECT keyword, sentiment, location, imgpath, CONVERT_TZ(`time`,'+00:00','-08:00') as `time` FROM queries WHERE uid = :uid AND imgpath IS NOT NULL ORDER BY `time` DESC");
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
											echo "<div class='col'><p><a href='home'>Go make some searches!</a> We'll keep track of them there</p></col>";
										}
										foreach ($most_recent_queries as $key => $value) {
											$base64 = file_get_contents('maps/'.$value['imgpath']);
											$time = date("M d, Y, h:i:s a", strtotime($value['time']));
											if ($value['sentiment'] < 0.40) {
												$sentiment = '<i class="fas fa-arrow-down"></i> <span class="text-danger">negative ('.$value['sentiment'].')</span>';
											} else if ($value['sentiment'] >= 0.45 && $value['sentiment'] < 0.55) {
												$sentiment = '<i class="fas fa-arrow-right"></i> <span class="text-dark">neutral ('.$value['sentiment'].')</span>';
											} else {
												$sentiment = '<i class="fas fa-arrow-up"></i> <span class="text-success">positive ('.$value['sentiment'].')</span>';
											}
											echo "<div class='col-sm-12 col-md-4 map-image'>

											<div class='card'>
											  <img class='card-img-top' src='".$base64."'>
											  <div class='card-body'>
											    <p class='card-text'><strong>Keyword: </strong>".$value['keyword']."<br><strong>Location: </strong>".$value['location']."<br><strong>Sentiment: </strong>".$sentiment."<br><strong>Date: </strong>".$time."</p>
											  </div>
											</div>

											</div>";
										}
									}
									?>
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

	$(document).ready(function() {
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
			$('.navbar-collapse').toggleClass("padded");
		});
	});
	</script>
</body>

</html>
