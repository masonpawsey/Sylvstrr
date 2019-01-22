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

// Add user action to log
$statement = $dbh->prepare('INSERT INTO user_log (uid, ip, agent, `time`, action) VALUES (:uid, :ip, :agent, NOW(), :action)');
$statement->execute([
	'uid' => $auth->getCurrentUser()['uid'],
	'ip' => $_SERVER['REMOTE_ADDR'],
	'agent' => $_SERVER['HTTP_USER_AGENT']??null,
	'action' => 'profile.php'
]);

if(strlen(filter_var(str_replace(array('+','-'), '', $_POST['phone']), FILTER_SANITIZE_NUMBER_INT)) != 10) {
	print_r("error");
} else {
	$verify_code = generateCode(4);
	$statement = $dbh->prepare('UPDATE phpauth_users SET verify_code = :verify_code WHERE id = :id');
	$statement->execute([
		'id' => $auth->getCurrentUser()['uid'],
		'verify_code' => $verify_code
	]);

	$client = new Client($account_sid, $auth_token);
	$client->messages->create($_POST['phone'], array( 'from' => $twilio_number, 'body' => 'Verify your sylvstrr number with the code: ' . $verify_code));

	// Clean up formatting from the phone number
	$_SESSION['phone'] = filter_var(str_replace(array('+','-'), '', $_POST['phone']), FILTER_SANITIZE_NUMBER_INT);
	print_r($verify_code);
}

function generateCode($length) {
	$code = '';
	for ($i=0; $i < $length; $i++) { 
		$code .= rand(0,9);
	}
	return $code;
}
?>