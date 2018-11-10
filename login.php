<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

function error($error) {
	die(json_encode($error));
}

// Never trust the client...
// Validate against an empty email
if(empty($_POST['email'])) {
	error(array("error" => true, "title" => "Do you even form?", "message" => "Please enter an email"));
}

// Validate against an empty password
if(empty($_POST['password'])) {
	error(array("error" => true, "title" => "Do you even form?", "message" => "Please enter a password"));
}

// If they've made it this far, check their account!
$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

$login = $auth->login($_POST['email'], $_POST['password']);
// Check for errors on registration attempt
print_r(json_encode($login));
if($login['error'] === false) {
	$_SESSION['hash'] = $login['hash'];
}
?>