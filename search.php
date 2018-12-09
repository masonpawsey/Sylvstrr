<?php
session_start();

if(empty($_POST)) {
	header("Location: ../");
	die();
}

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/css/mdb.min.css" rel="stylesheet">
<style type="text/css">
	body {
		background: #263238;
		color: #fff;
	}
	.highlight {
		color: red;
	}
	a {
		color: #fff;
	}
	a:visited {
		color: #fff;
	}
	pre {
		color: #bbb;
	}
</style>
<pre>
<?php

// The location gets passed to us as "City, Country". This will
// exploded that into an array of ["city", "country"]. We will 
// use that array to get the lat and long from the database
$location = explode(', ',$_POST['location']);
$city = $location[0];
$country = $location[1];

// If they don't provide a location, die
if(empty($latitude) || empty($longitude)) {
	echo "Error";
	die();
}

// Connect to the database
require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$statement = $dbh->prepare('SELECT latitude, longitude FROM location WHERE city = :city AND country = :country');
$statement->execute([
	'city' => $city,
	'country' => $country
]);
// Print errors, if they exist
if($statement->errorInfo()[0] != "00000") {
	print_r($statement->errorInfo());
	die();
} else {
	// Store the lat and long from the database to build our query
	$results = $statement->fetch(PDO::FETCH_ASSOC);
	$latitude = $results['latitude'];	
	$longitude = $results['longitude'];
}

// The passed keywords (our query doesn't care how this is formatted -
// so spaces will separate the keywords)
$keyword = "'".$_POST['keyword']."'";
$session_id = $_SESSION['id'];
// This is the file that we will store these Tweets in
$file_id = uniqid();

// Run this script as sudo because we hate security :)
// (and because ./scrape-twitter is protected and www-data can't get to it)
$raw = shell_exec("sudo /var/www/html/run.sh $latitude $longitude $keyword");

$keyword = $_POST['keyword'];
// Check for the keyword
$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);
// Check for the keyword without spaces
$keyword = str_replace(' ', '', $keyword);
$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);
print_r(json_decode($result, true));

$dbh = null;

// echo "done!";

?></pre>
<a href="..">Back</a>
