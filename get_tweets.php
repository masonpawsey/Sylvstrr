<?php

// Pass the number of Tweets to scrape via the CLI
// TO DO: pass the entire query via the CLI for faster searches
if (!empty($argv[1])) {
	$count = $argv[1];
} else {
    $count = 100;
}

if (!empty($argv[2])) {
    $query = $argv[2];
} else {
    $query = '';
}

// Keep track of time
$startTime = microtime(true);

// Run the scraper and store the tweets into a var to be worked on
$raw_tweets = shell_exec("/usr/local/bin/scrape-twitter search --query '".$query." lang:en' --type latest --count ".$count." | jq '[.[] | {screenName, id, text, time}]' 2>&1");

// Connect to the database
$user = 'root';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

// Put all of the tweets from JSON into an array we can iterate over
$tweets = json_decode($raw_tweets,true);

$counter = 0;

foreach ($tweets as $key => $value) {

	$statement = $dbh->prepare('INSERT INTO tweets (id, screenName, `text`, `time`)
	    VALUES (:id, :screenName, :text, :time)');

	$statement->execute([
	    'id' => $value['id'],
	    'screenName' => $value['screenName'],
	    'text' => $value['text'],
	    'time' => date("Y-m-d H:i:s", strtotime($value['time']))
    	]);

	// Print errors, if they exist
	if($statement->errorInfo()[0] != "00000") {
		print_r($statement->errorInfo());
	} else {
		$counter++;
	}
}

$dbh = null;

print_r("Inserted: " . $counter . " tweets in " . (microtime(true) - $startTime) ." seconds\n");
?>
