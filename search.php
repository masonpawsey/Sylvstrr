<?php

// Authenticate me!
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
	header("HTTP/1.1 401 Unauthorized");
	exit;
}

// The location gets passed to us as "City, Country". This will
// exploded that into an array of ["city", "country"]. We will 
// use that array to get the lat and long from the database
$location = explode(', ',$_POST['location']);
$city = $location[0];
$country = $location[1];

// If they don't provide a location, die
if(empty($city) || empty($country)) {
	echo "Error, please provide a location";
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

// Add this query to the users records in the database

// Run this script as sudo because we hate security :)
// (and because ./scrape-twitter is protected and www-data can't get to it)
$raw = shell_exec("sudo /var/www/html/run.sh $latitude $longitude $keyword");

$keyword = $_POST['keyword'];
// Check for the keyword
$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);
// Check for the keyword without spaces
$keyword = str_replace(' ', '', $keyword);
$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);
print_r(json_encode($result));

$dbh = null;


?>