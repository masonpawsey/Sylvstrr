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

// Search for cached queries
// 6 hours of data is considered "fresh"
$statement = $dbh->prepare('SELECT sentiment FROM queries WHERE location = :location AND keyword = :keyword AND `time` > DATE_ADD(NOW(), INTERVAL -6 HOUR) AND cached = 0 ORDER BY `time` DESC LIMIT 1');
$statement->execute([
	'location' => $_POST['location'],
	'keyword' => $_POST['keyword']
]);

$caches = $statement->fetch(PDO::FETCH_ASSOC);
if($caches['sentiment']) {
	$xml = '<query was cached>';
	$sentiment = $caches['sentiment'];
	// Add this query to our table of queries
	$statement = $dbh->prepare('INSERT INTO queries (uid, ip, agent, `time`, keyword, location, sentiment, cached) VALUES (:uid, :ip, :agent, NOW(), :keyword, :location, :sentiment, :cached);');
	$statement->execute([
		'uid' => $auth->getCurrentUser()['uid'],
		'ip' => $_SERVER['REMOTE_ADDR'],
		'agent' => $_SERVER['HTTP_USER_AGENT']??null,
		'keyword' => $_POST['keyword'],
		'location' => $_POST['location'],
		'sentiment' => $sentiment,
		'cached' => 1
	]);
	// Print errors, if they exist
	if($statement->errorInfo()[0] != "00000") {
		print_r($statement->errorInfo());
		die();
	}
	$cached = true;
} else {
	// If caches don't exist for that query, run another one!
	// Run this script as sudo because we hate security :)
	// (and because ./scrape-twitter is protected and www-data can't get to it)
	$raw = shell_exec("sudo /var/www/html/run.sh $latitude $longitude $keyword");

	$keyword = $_POST['keyword'];
	// Check for the keyword
	$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);
	// Check for the keyword without spaces
	$keyword = str_replace(' ', '', $keyword);
	$result = preg_replace("/\p{L}*?".preg_quote($keyword)."\p{L}*/ui", "<span class='highlight'>$0</span>", $raw);

	$xml = '<?xml version="1.0" encoding="UTF-8"?><tweets xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';

	foreach (json_decode($raw) as $key => $value) {
		$xml .= '<tweet><text>'.htmlspecialchars($value->text).'</text><sentiment></sentiment></tweet>';
	}

	$xml .= '</tweets>';

	// Generates tweets.xml file for Zak's AI engine `python3 run.py`
	// $dir = uniqid();

	// if (!file_exists('ai-engine/'.$dir)) {
	// 	mkdir('ai-engine/'.$dir, 0775, true);
	// }

	// $file = fopen("ai-engine/" . $dir . "/tweets.xml" , "w");
	// fwrite($file, $xml);
	// fclose($file);

	// xml is passed to AI engine
	// JSON is passed to browser FOR THE MOMENT - will be replaced with just the resulting sentiment

	// Post to Azure for sentiment analysis
	$prefix = '{"documents": ';
	$suffix = '}';

	$body = $prefix.$raw.$suffix;

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://westus.api.cognitive.microsoft.com/text/analytics/v2.0/sentiment');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	$headers = array();
	$headers[] = 'Content-Type: application/json';
	$headers[] = 'Ocp-Apim-Subscription-Key: ' . $azure_key;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$azure_result = curl_exec($ch);

	$array = json_decode($azure_result, true)['documents'];
	$score = 0;
	foreach ($array as $key => $value) {
		$score += $value['score'];
	}

	$sentiment = $score/count($array);

	// Add this query to our table of queries
	$statement = $dbh->prepare('INSERT INTO queries (uid, ip, agent, `time`, keyword, location, sentiment, cached) VALUES (:uid, :ip, :agent, NOW(), :keyword, :location, :sentiment, :cached);');
	$statement->execute([
		'uid' => $auth->getCurrentUser()['uid'],
		'ip' => $_SERVER['REMOTE_ADDR'],
		'agent' => $_SERVER['HTTP_USER_AGENT']??null,
		'keyword' => $_POST['keyword'],
		'location' => $_POST['location'],
		'sentiment' => $sentiment,
		'cached' => 0
	]);
	// Print errors, if they exist
	if($statement->errorInfo()[0] != "00000") {
		print_r($statement->errorInfo());
		die();
	}

	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
	$cached = false;
}

// The array we return holds the resulting Tweets as well as coordinates for the location
// they passed so we can update the map on home.php, and their most recent searches
$return = [$result, [$longitude, $latitude], $most_recent_queries, $xml, $sentiment, $dbh->lastInsertId(), $cached];
print_r(json_encode($return));

$dbh = null;


?>