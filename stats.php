<?php

// Connect to the database
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

// Count the positive Tweets
$statement = $dbh->prepare('SELECT count(*) FROM tweets WHERE sentiment = "positive"');
$statement->execute();
$positive = $statement->fetch(PDO::FETCH_ASSOC)['count(*)'];


// Count the neutral Tweets
$statement = $dbh->prepare('SELECT count(*) FROM tweets WHERE sentiment = "neutral"');
$statement->execute();
$neutral = $statement->fetch(PDO::FETCH_ASSOC)['count(*)'];

// Count the negative Tweets
$statement = $dbh->prepare('SELECT count(*) FROM tweets WHERE sentiment = "negative"');
$statement->execute();
$negative = $statement->fetch(PDO::FETCH_ASSOC)['count(*)'];

// Count the Tweets that haven't been analyzed yet
$statement = $dbh->prepare('SELECT count(*) FROM tweets WHERE sentiment IS NULL');
$statement->execute();
$unknown = $statement->fetch(PDO::FETCH_ASSOC)['count(*)'];

// Count the total Tweets
$statement = $dbh->prepare('SELECT count(*) FROM tweets');
$statement->execute();
$total = $statement->fetch(PDO::FETCH_ASSOC)['count(*)'];


$dbh = null;

$stats = ['positive' => $positive, 'neutral' => $neutral, 'negative' => $negative, 'unknown' => $unknown, 'total' => $total, 'done' => $total-$unknown];

echo json_encode($stats);

?>