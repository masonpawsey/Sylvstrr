<html>
<head>
	<meta charset='utf-8' />
	<title>sylvstrr - 404</title>
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
	<link rel="icon" type="image/png" href="./assets/favicon.png">
	<style type="text/css">
		#map_404 {
		  position:absolute;
		  top:0;
		  bottom:0;
		  width: 100%;
		}
		.text-404 {
			margin: 0 auto;
			padding: 0.5em;
			background: #000;
		}
		.container {
			background: transparent;
			position: absolute;
			z-index: 1;
			color: #fff;
			font-size: 3em;
		}
	</style>
</head>

<body>
	<main role="main" id="map_404">
		<div class="container d-flex h-100">
		    <div class="row justify-content-center align-self-center text-center text-404">
		    	<div>
		    		page not found
		    	</div>
		    	<div>
		    		<a href="home">land ahoy</a>
		    	</div>
		    </div>
		</div>
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
		container: 'map_404', // container id
		style: 'mapbox://styles/masonpawsey/cjnzi73pd85jn2rmnm1oj9g65', // stylesheet location
		center: [lng, lat], // starting position [lng, lat]
		zoom: zoom,
		interactive: false
	});

	$(document).ready(function() {
		console.log('lng, lat', lng, lat);
	});
	</script>
</body>

</html>
