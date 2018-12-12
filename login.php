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
require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

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
?>