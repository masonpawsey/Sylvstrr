<?php 
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

// Grab the oldest free Tweet
$statement = $dbh->prepare('SELECT * FROM tweets WHERE sentiment IS NULL AND in_use = 0 ORDER BY RAND() LIMIT 1');
$statement->execute();
$record = $statement->fetch();

// Mark this record as "in use"
$statement = $dbh->prepare('UPDATE tweets SET in_use=1 WHERE id=:id');
$statement->execute([
    'id' => $record['id']
]);

// Send this back to the client
echo json_encode($record);

$dbh = null;
?>