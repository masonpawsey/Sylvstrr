<?php

// Connect to the database
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

// Count the positive Tweets
$statement = $dbh->prepare('SHOW TABLE STATUS');
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC)[8]['Rows'];

print_r(number_format($result));

?>