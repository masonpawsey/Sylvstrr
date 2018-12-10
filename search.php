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

// Add this query to our table of queries
$statement = $dbh->prepare('INSERT INTO queries (uid, ip, agent, `time`, keyword, location) VALUES (:uid, :ip, :agent, NOW(), :keyword, :location);');
$statement->execute([
	'uid' => $auth->getCurrentUser()['uid'],
	'ip' => $_SERVER['REMOTE_ADDR'],
	'agent' => $_SERVER['HTTP_USER_AGENT']??null,
	'keyword' => $_POST['keyword'],
	'location' => $_POST['location']
]);
// Print errors, if they exist
if($statement->errorInfo()[0] != "00000") {
	print_r($statement->errorInfo());
	die();
}

// Get most recent queries
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
}

// Run this script as sudo because we hate security :)
// (and because ./scrape-twitter is protected and www-data can't get to it)
$raw = shell_exec("sudo /var/www/html/run.sh $latitude $longitude $keyword");

$keyword = $_POST['keyword'];
// Check for the keyword
$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);
// Check for the keyword without spaces
$keyword = str_replace(' ', '', $keyword);
$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);

// The array we return holds the resulting Tweets as well as coordinates for the location
// they passed so we can update the map on home.php, and their most recent searches
$return = [$result, [$longitude, $latitude], $most_recent_queries];

print_r(json_encode($return));

$dbh = null;


?>