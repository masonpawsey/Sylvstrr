<?php
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use ZxcvbnPhp\Zxcvbn as Zxcvbn;

function error($error) {
	die(json_encode($error));
}

// Never trust the client...
// Validate against an empty email
if(empty($_POST['email'])) {
	error(array("error" => true, "title" => "Do you even form?", "message" => "Please enter an email"));
}

// Validate against an invalid email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { 
	error(array("error" => true, "title" => "Nice try", "message" => "Please enter a <strong>valid</strong> email"));
}

// Validate against an empty password
if(empty($_POST['password'])) {
	error(array("error" => true, "title" => "Do you even form?", "message" => "Please enter a password"));
}

// Use zxcvbn to validate the strength of passwords. We will require at least a strength of 2
$zxcvbn = new Zxcvbn();
$passwordStrength =  $zxcvbn->passwordStrength($_POST['password']);

if($passwordStrength['score'] < 2) {
	error(array("error" => true, "title" => "Wimpy password!", "message" => "You could crack that thing in " . round($passwordStrength['crack_time']/60,2) . " minutes"));
}

$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

// If the account doesn't already exist, make one!
$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

$registered = $auth->register($_POST['email'], $_POST['password'], $_POST['password']);

if($registered['error'] === true) {
	error(array("error" => true, "message" => $registered['message']));
} else {
	$login = $auth->login($_POST['email'], $_POST['password']);
	if($login['error'] === false) {
		$_SESSION['hash'] = $login['hash'];
	}
	// Log in and then redirect (the referring page will handle the redirect)
	print_r(json_encode(array("error" => false, "title" => "Success", "message" => "Account has been created! We are logging you in")));
}
?>