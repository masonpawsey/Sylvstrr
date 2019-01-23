<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use ZxcvbnPhp\Zxcvbn as Zxcvbn;

function error($error) {
	die(json_encode($error));
}

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if(!$auth->comparePasswords($auth->getCurrentUser()['uid'], $_POST['current'])) {
	error(array("error" => true, "message" => "Incorrect password"));
} else {
	$zxcvbn = new Zxcvbn();
	$passwordStrength =  $zxcvbn->passwordStrength($_POST['new']);

	if($passwordStrength['score'] < 2) {
		error(array("error" => true, "title" => "Wimpy password!", "message" => "You could crack that thing in " . round($passwordStrength['crack_time']/60,2) . " minutes"));
	}

	$result = $auth->changePassword($auth->getCurrentUser()['uid'], $_POST['current'], $_POST['new'], $_POST['new2'], NULL);

	if($result['error'] != NULL) {
		error(array("error" => true, "title" => "Uh oh", "message" => $result['message']));
	} else {
		error(array("error" => false, "title" => "Success", "message" => $result['message']));
	}
}

?>