<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

function error($error) {
	die(json_encode($error));
}

if(empty($_POST['password'])) {
	error(array("error" => true, "title" => "Error", "message" => "Please enter your password"));
}

// Add user action to log
$statement = $dbh->prepare('INSERT INTO user_log (uid, ip, agent, `time`, action) VALUES (:uid, :ip, :agent, NOW(), :action)');
$statement->execute([
	'uid' => $auth->getCurrentUser()['uid'],
	'ip' => $_SERVER['REMOTE_ADDR'],
	'agent' => $_SERVER['HTTP_USER_AGENT']??null,
	'action' => 'deleteaccount.php'
]);

$result = $auth->deleteUser($auth->getCurrentUser()['uid'], $_POST['password']);

if($result['error'] === false) {
	session_destroy();
}

print_r(json_encode($result));
?>