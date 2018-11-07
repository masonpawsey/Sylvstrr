<?php

// This file will take all of the entries in our database that have been analyzed
// and generate  CSV that we can use to train our AI engine

// Connect to the database
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$statement = $dbh->prepare('SELECT sentiment, text FROM tweets WHERE sentiment IS NOT NULL');
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

$file = fopen("corpus.csv", "w") or die("Unable to open file!");
chmod(__DIR__."/corpus.csv", 0777);
foreach ($results as $key => $value) {
	$text =  "\"".$value['sentiment']."\",\"".str_replace('"', "'", $value['text'])."\"\n";
	fwrite($file, $text);
}

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename("corpus.csv") . '"');
header('Content-Transfer-Encoding: binary');
readfile("corpus.csv");
fclose($file);

?>