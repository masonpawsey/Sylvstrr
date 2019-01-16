<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Twilio\Rest\Client;

function return_result($result) {
	die(json_encode($result));
}

// Never trust the client...
// Validate against an empty email
if(empty($_POST['email'])) {
	return_result(array("error" => true, "title" => "Do you even form?", "message" => "Please enter an email"));
}

// Validate against an empty password
if(empty($_POST['password'])) {
	return_result(array("error" => true, "title" => "Do you even form?", "message" => "Please enter a password"));
}

// If they've made it this far, check their account!
require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

// If the user has a phone number in the database, they have 2FA login enabled
$statement = $dbh->prepare("SELECT phone FROM phpauth_users WHERE email = :email");
$statement->execute([
	'email' => $_POST['email']
]);
// Print errors, if they exist
if($statement->errorInfo()[0] != "00000") {
	return_result(array("error" => true, "title" => "Database error", "message" => $statement->errorInfo()));
	die();
} else {
	// The phone number could be NULL or a 10-digit number
	$phone = $statement->fetch(PDO::FETCH_ASSOC)['phone'];
}

// If a phone number exists for this user, verify the code we send
if(isset($phone)) {
	// Send code, check back on login page
	if(strlen($_POST['code']) == 0) {
		$verify_code = generateCode(4);
		$statement = $dbh->prepare('UPDATE phpauth_users SET verify_code = :verify_code WHERE email = :email');
		$statement->execute([
			'email' => $_POST['email'],
			'verify_code' => $verify_code
		]);
		// Send the user the text
		$client = new Client($account_sid, $auth_token);
		$client->messages->create($phone, array( 'from' => $twilio_number, 'body' => 'Welcome to Sylvester! Verify your number with the code: ' + $verify_code));
		// Show them the last 2 digits of the phone number we sent a code to
		return_result(array("2fa" => true, "phone" => substr($phone, -2)));
	} else {
		// Check code
		$statement = $dbh->prepare("SELECT verify_code FROM phpauth_users WHERE email = :email");
		$statement->execute([
			'email' => $_POST['email']
		]);

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		// If the code we stored is the same code they supplied, log them in
		if($result['verify_code'] === $_POST['code']) {
			login($auth, $dbh);
		} else {
		// Code mismatch, error out
			return_result(array("error" => true, "title" => "Oops", "message" => "Email address / password are invalid."));
		}
	}
} else {
	// If a phone number doesn't exist for this user, check just their login
	login($auth, $dbh);
}

function generateCode($length) {
	$code = '';
	for ($i=0; $i < $length; $i++) { 
		$code .= rand(0,9);
	}
	return $code;
}

function login($auth, $dbh) {
	$login = $auth->login($_POST['email'], $_POST['password']);

	// Add user action to log
	$statement = $dbh->prepare('INSERT INTO user_log (uid, ip, agent, `time`, action) VALUES (:uid, :ip, :agent, NOW(), :action)');
	$statement->execute([
	    'uid' => $auth->getCurrentUser()['uid'],
	    'ip' => $_SERVER['REMOTE_ADDR'],
	    'agent' => $_SERVER['HTTP_USER_AGENT']??null,
	    'action' => 'login.php'
	]);

	// Check for errors on registration attempt
	print_r(json_encode($login));
	$_SESSION['hash'] = $login['hash'];
}
?>