<?php

require_once 'vendor/autoload.php';


@session_start();
date_default_timezone_set('Europe/Berlin');

$check = getenv("db-fallback");

if ($check == 0) {
	//CLEARDB 
} else {
	//JawDB fallback
}

if (mysqli_connect_errno()) {
	@http_response_code(500);
	echo "Keine Datenbankverbindung: %s\n" . mysqli_connect_error ." - ". $db->error;
	die();
}

function db($dbq, $debug = 0) {
	GLOBAL $db;

	if ($db_result = $db->query($dbq)) {

		$ar_result = array();
		$i = 0;

		if (!is_bool($db_result)) {
			while($row = $db_result->fetch_assoc()) {
				$ar_result[$i] = $row;
				$i++;
			}
			return $ar_result;
		} else {
			return $db_result;
		}
	} else {
		if ($debug) {
			echo "ERROR Datenbank Abfrage: \"$dbq\"<br /><br />";
			@http_response_code(500);
			die($db->error);
		} else {
			@http_response_code(500);
			return false;
		}
	}
}	

function esc($dbd) {
	GLOBAL $db;
	return $db->real_escape_string($dbd);
}

//von http://php.net/manual/en/function.crypt.php
function generate_hash($password, $cost=11){ 
	$salt=substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
	$salt=str_replace("+",".",$salt);
	$param='$'.implode('$',array(
					"2y", //select the most secure version of blowfish (>=PHP 5.3.7)
					str_pad($cost,2,"0",STR_PAD_LEFT), //add the cost in two digits
					$salt //add the salt
				));

	return crypt($password,$param);
}

function validate_pw($password, $hash){
	return crypt($password, $hash)==$hash;
}



Raven_Autoloader::register();
$client = new Raven_Client('https://15da66d440c144ddb5a5b9ff6b2b7042:c7bb32e0137b4cd59a52beb6f1969ada@sentry.io/112192');
$error_handler = new Raven_ErrorHandler($client);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();


require_once 'class.php';

?>