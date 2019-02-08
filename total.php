<?php

// Connect to the database
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

// Count the positive Tweets
$statement = $dbh->prepare('SHOW TABLE STATUS');
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC)[8]['Rows'];

exec("ls /var/www/html/trainer/jsons | wc -l 2>&1", $output, $return_var);

echo "Current: " . number_format($result) . "<br>Pending JSONs: " . $output['0'] . "<br>Estimated: +" . number_format($output[0]*1150) . "<br>Assumed total: " . number_format($result + $output[0]*1150);

?>