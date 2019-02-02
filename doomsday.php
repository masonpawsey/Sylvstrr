<?php

// Pass the number of Tweets to scrape via the CLI
if (!empty($argv[1])) {
	$count = $argv[1];
} else {
	$count = 10000;
}

if (!empty($argv[2])) {
	$query = $argv[2];
} else {
	$query = '';
}

// Connect to the database
$user = 'root';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$countries = ['United States', 'Australia','United Kingdom','Canada'];

$statement = $dbh->prepare('SELECT latitude, longitude FROM location WHERE country = :country ORDER BY RAND() LIMIT 1');
$statement->execute(array("country" => $countries[array_rand($countries)]));
$result = $statement->fetch(PDO::FETCH_ASSOC);

$lat = $result['latitude'];
$long = $result['longitude'];

// Keep track of time
$startTime = microtime(true);

// Run the scraper and store the tweets into a var to be worked on
$raw_tweets = shell_exec("/usr/local/bin/scrape-twitter search --query 'lang:en geocode:".$lat.",".$long.",50km' --type latest --isRetweet false  --count ".$count." | jq '[.[] | {screenName, id, text, time}]' 2>&1");

// Put all of the tweets from JSON into an array we can iterate over
$tweets = json_decode($raw_tweets,true);

$counter = 0;

if(!empty($tweets)) {

	foreach ($tweets as $key => $value) {
		$statement = $dbh->prepare('INSERT INTO scraped_tweets (id, screenName, `text`, `time`, latitude, longitude)
		    VALUES (:id, :screenName, :text, :time, :lat, :long)');

		$statement->execute([
			'id' => $value['id'],
			'screenName' => $value['screenName'],
			'text' => $value['text'],
			'time' => date("Y-m-d H:i:s", strtotime($value['time'])),
			'lat' => $lat,
			'long' => $long
		]);

		// Print errors, if they exist
		if($statement->errorInfo()[0] != "00000") {
			print_r($statement->errorInfo());
		} else {
			$counter++;
		}
	}	
	$log = "Inserted: " . $counter . " tweets from " . $lat . ", " . $long . " in " . (microtime(true) - $startTime) ." seconds at " . date("Y-m-d H:i:s");

} else {
	$log = "No tweets found for " . $lat . ", " . $long . " in " . (microtime(true) - $startTime) ." seconds at " . date("Y-m-d H:i:s");
}

$dbh = null;
$myfile = file_put_contents('/var/www/html/trainer/scrape.log', $log.PHP_EOL , FILE_APPEND | LOCK_EX);

print_r($log);
die();
?>
