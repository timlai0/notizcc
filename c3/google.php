<?php 
//timlai.de 2017

require_once __DIR__.'/google-api-php-client-2.1.3/vendor/autoload.php';

define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', __DIR__ . '/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
	Google_Service_Calendar::CALENDAR)
));


/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
	$client = new Google_Client();
	$client->setApplicationName(APPLICATION_NAME);
	$client->setScopes(SCOPES);
	$client->setAuthConfig(CLIENT_SECRET_PATH);
	$client->setAccessType('offline');
	// Load previously authorized credentials from a file.
	$credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
	if (file_exists($credentialsPath)) {
		$accessToken = json_decode(file_get_contents($credentialsPath), true);
	} else {

	if (php_sapi_name() != 'cli') {
		throw new Exception('This application must be run on the command line.');
	}
		// Request authorization from the user.
		$authUrl = $client->createAuthUrl();
		printf("Open the following link in your browser:\n%s\n", $authUrl);
		print 'Enter verification code: ';
		$authCode = trim(fgets(STDIN));

		// Exchange authorization code for an access token.
		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

		// Store the credentials to disk.
		if(!file_exists(dirname($credentialsPath))) {
			mkdir(dirname($credentialsPath), 0700, true);
		}
		file_put_contents($credentialsPath, json_encode($accessToken));
		printf("Credentials saved to %s\n", $credentialsPath);
	}
	$client->setAccessToken($accessToken);

	// Refresh the token if it's expired.
	if ($client->isAccessTokenExpired()) {
		$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
		file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
	}
	return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
	$homeDirectory = getenv('HOME');
	if (empty($homeDirectory)) {
		$homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
	}
	return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

	$client->setUseBatch(true);
	$batch = new Google_Http_Batch($client);

$calendarId = 'mvrfucujehb9cd3qi7bfvs4t6s@group.calendar.google.com';


function add_event($stamp, $start, $end, $uid, $desc, $summ, $location) {


	global $service, $calendarId, $client, $batch;


	$event = new Google_Service_Calendar_Event(array(
		'iCalUID' => $uid,
		'summary' => $summ,
		'location' => $location,
		'description' => $desc,
		'start' => array(
			'dateTime' => date(DateTime::ATOM, strtotime($start))
		),
		'end' => array(
			'dateTime' => date(DateTime::ATOM, strtotime($end))
		),
		'sequence' => time()));

	

	$req = $service->events->import($calendarId, $event);

	$batch->add($req, $uid);
}

function delete_event($evid) {
	global $service, $calendarId, $client, $batch;
	$req = $service->events->delete($calendarId, $evid, array('sendNotifications' => 1));
	$batch->add($req);
}


function list_event($date) {
	global $service, $calendarId, $client, $batch;

	$results = $batch->execute();

	echo '_____________________vor ein Listevent call________________';
	print_r($results);
	echo '_____________________';


	$client->setUseBatch(false);


if($date->format('w') == 0) {
		$nextSunday = date(DateTime::ATOM, strtotime('today', $date->format('U')));
} else {
		$nextSunday = date(DateTime::ATOM, strtotime('next sunday', $date->format('U')));
}

if($date->format('w') == 1) {
	$lastMonday = date(DateTime::ATOM, strtotime('today', $date->format('U')));
} else {
	$lastMonday = date(DateTime::ATOM, strtotime('last monday', $date->format('U')));
}

	$events = $service->events->listEvents($calendarId, array('timeMin' => $lastMonday, 'timeMax' => $nextSunday));

	$client->setUseBatch(true);
	$batch = new Google_Http_Batch($client);
	return $events;

}




function pushbullet($email, $type, $title, $body, $url = '') {

	$apikey = 'o.###';


	$data = json_encode(array('email' => $email, 'type' => $type, 'title' => $title, 'body' => $body, 'url' => $url, 'source_device_iden' => 'ujwFUpFN7a8sjzYqxq6psy'));

	$target = 'https://api.pushbullet.com/v2/pushes';

	$curl_connection = curl_init($target);

	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);

	curl_setopt($curl_connection, CURLOPT_USERPWD, $apikey);

	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl_connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $data);

	$result = curl_exec($curl_connection);

	if (curl_errno($curl_connection)) {
		print_r(curl_getinfo($curl_connection));
		echo curl_error($curl_connection);
		die();
	}

	curl_close($curl_connection);

	return json_decode($result);
}



?>