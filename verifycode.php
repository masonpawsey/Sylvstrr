<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Twilio\Rest\Client;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
	header("Location: index.php");
	die('Forbidden');
}

$statement = $dbh->prepare("SELECT verify_code FROM phpauth_users WHERE id = :id");
$statement->execute([
	'id' => $auth->getCurrentUser()['uid']
]);
$result = $statement->fetch(PDO::FETCH_ASSOC);


if($result['verify_code'] === $_POST['code']) {
	if($_POST['enable'] == 'true') {
		// Store user phone number in database
		$statement = $dbh->prepare('UPDATE phpauth_users SET phone = :phone, verify_code = NULL WHERE id = :id');
		$statement->execute([
			'id' => $auth->getCurrentUser()['uid'],
			'phone' => $_SESSION['phone']
		]);
		unset($_SESSION['phone']);
		print_r('true');
	} else {
		// Remove user phone number from database
		$statement = $dbh->prepare('UPDATE phpauth_users SET phone = NULL, verify_code = NULL WHERE id = :id');
		$statement->execute([
			'id' => $auth->getCurrentUser()['uid']
		]);
		unset($_SESSION['phone']);
		print_r('true');
	}
} else {
	print_r('false');
}


?>