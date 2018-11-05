<?php 
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);


// Update this particular Tweet
$statement = $dbh->prepare('UPDATE tweets SET in_use = 0, sentiment = :sentiment WHERE id=:id');
$statement->execute([
	'sentiment' => $_POST['sentiment'],
    'id' => $_POST['id']
]);

// Send this back to the client
echo json_encode("Updated tweet.");

$dbh = null;
?>