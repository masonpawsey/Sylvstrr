<?php
// Authenticate me!
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
	header("HTTP/1.1 401 Unauthorized");
	exit;
}

$file = uniqid();
$handler = fopen('maps/' . $file, 'w');
fwrite($handler, $_POST['map_code']);
fclose($handler);

$statement = $dbh->prepare('UPDATE queries SET imgpath = :imgpath WHERE id = :id');
$statement->execute([
	'imgpath' => $file,
	'id' => $_POST['last_inserted']
]);

?>