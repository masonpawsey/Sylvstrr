<?php

// Connect to the database
require_once('../credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

for ($i=0; $i < count(glob('./jsons/*.json')); $i++) { 

	$files = glob('./jsons/*.json');

	// Keep track of time
	$startTime = microtime(true);
	array_multisort( array_map( 'filemtime', $files ), SORT_NUMERIC, SORT_ASC, $files);

	if(empty($files)) {
		echo "no files\n";
		die();
	}

	$pieces = explode("_", $files[0]);
	print_r($pieces);

	$lat = $pieces[1];
	$long = $pieces[2];

	$json = file_get_contents($files[0]);
	$tweets = json_decode($json,true);

	$counter = 0;

	print_r($tweets[0]);

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

	unlink($files[0]);
	$log = "Inserted: " . $counter . " tweets from " . $lat . ", " . $long . " in " . (microtime(true) - $startTime) ." seconds at " . date("Y-m-d H:i:s");
	$myfile = file_put_contents('/var/www/html/trainer/scrape.log', $log.PHP_EOL , FILE_APPEND | LOCK_EX);
	print_r($log);
}
?>
