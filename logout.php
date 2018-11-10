<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$user = 'phpmyadmin';
$pass = '25dba36cbfa5b0a17a03a7fb8e10c96496de6d99b5459fc2';
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

$auth->logout($_SESSION['hash']);
session_destroy();
header("Location: ../");
die('You have been logged out');
?>