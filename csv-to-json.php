<pre>




<?php
	$csv = file_get_contents("new_tweets.txt");
	$csv = preg_replace('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) /', '\n\n$1|||', $csv);
	$csv = array_values(array_filter(explode('\n\n', $csv)));
	$array_of_tweets = [];

	foreach ($csv as $key => $value) {
		$tweet_data = explode('|||', str_replace(array("\n", "\t", "\r"), '', $value));
		$array_of_tweets[$key] = array('date' => $tweet_data[0], 'tweet' => $tweet_data[1]);
	}

	print_r($array_of_tweets);
?>
	



</pre>