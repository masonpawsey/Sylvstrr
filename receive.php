<?php

require_once 'vendor/autoload.php'; // Loads the library
use Twilio\Twiml;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

$response = new Twiml;
$body = $_REQUEST['Body'];
$incomingNumber = $_REQUEST['From'];
$incomingNumber = substr($incomingNumber, -10);
if(strtolower(trim($body)) == 'date') {
		$statement = $dbh->prepare('SELECT `dt` FROM phpauth_users WHERE phone = :phone');
		$statement->execute([
			'phone' => $incomingNumber
		]);
		$result = $statement->fetch(PDO::FETCH_ASSOC)['dt'];
		if(empty($result)) {
			$response->message('Oh no, your account was not found! Make an account here https://bit.ly/2WXqLYN or add your phone number to your account.');
		} else {
			$response->message('Your account was created on '.date("M d, Y", strtotime($result)).' at '.date("h:i:s a", strtotime($result)). '. Thanks for your dedication 🙏');
		}
} else {
	$response->message('Hmmm, we don\'t know that one yet 🤔');
}

print $response;

?>