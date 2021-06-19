<?php

// $debug = 0;

if (!isset($debug)) {
	ob_end_clean();
	header("Connection: close");
	ignore_user_abort(true);
	ob_start();
	$size = ob_get_length();
	header("Content-Length: $size");
	ob_end_flush(); 
	flush();
} else {
}

$offset = 2;


require_once 'google.php';
setlocale(LC_TIME, "de");

function untisToGCal($offset = 2) {

	$teacherIHaveInDe = array("WEN");
	$teacherIHaveInEn = array("STI");
	$classesIDontHave = array("+re", "+spa", "+sp");
	$teacherIDontHave = array();

	$date = new DateTime('+'.$offset.' days');
	$date->setTime(0,0,0);


	$url = "https://aoide.webuntis.com/WebUntis/Ical.do?elemType=1&elemId=1074&rpt_sd=".$date->format("Y-m-d");

	$curl_connection = curl_init($url);

	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);

	if (!empty($user)) {
		curl_setopt($curl_connection, CURLOPT_USERPWD, $user);
	}

	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);

	curl_setopt($curl_connection, CURLOPT_HTTPHEADER, array('Cookie: JSESSIONID=D9B63893536E4A9276CF144193CDA7EF; schoolname="_YmJzLWJ1cmdkb3Jm"', 'Referer: https://aoide.webuntis.com/WebUntis/?school=bbs-burgdorf^&navid=4'));

	$icsSource = explode("\n", trim(curl_exec($curl_connection)));

	if (curl_errno($curl_connection)) {
		print_r(curl_getinfo($curl_connection));
		echo curl_error($curl_connection);
		die();
	}

	curl_close($curl_connection);

	if (end($icsSource) != 'END:VCALENDAR') {
		die('ERROR: Quelle nicht verfügbar');
	} elseif (count($icsSource) == 2) {
		echo "in der Woche keine Events";
		return NULL;
	}

	//In Events aufteilen
	do {
		foreach ($icsSource as $key => $line ) {

			if (strpos($line, "BEGIN:VEVENT") !== false) {

				foreach ($icsSource as $key2 => $line2 ) {
					if (strpos($icsSource[$key2], "END:VEVENT") !== false) {
						break;
					}
				}

				$ar_ar_event[] = array_slice($icsSource, $key + 1, $key2 - $key -1);

				$icsSource = array_slice($icsSource, $key2 + 1);


				break;
			}
		}

	} while ($icsSource[0] != 'END:VCALENDAR');

	//richtige Array Keys setzen
	foreach ($ar_ar_event as $key1 => $ar_event) {

		foreach ($ar_event as $key2 => $info) {

			if (count($ar_line = explode(':', $info)) == 2) {
				$ar_ar_event2[$key1][$ar_line[0]] = trim($ar_line[1]);
			} else {
				$ar_ar_event2[$key1]['DESCRIPTION'] .= trim($ar_line[0]);

			}
		}
	}

	//Zwei Stunden hintereinander Entfernen
	foreach ($ar_ar_event2 as $key1 => $event1) {
		foreach ($ar_ar_event2 as $key2 => $event2) {
			if ($event2["DTSTART"] == $event1["DTEND"]) {
				$ar_ar_event2[$key1]["DTEND"] = $event2["DTEND"];
				unset($ar_ar_event2[$key2]);
				break;
			} 
		}
	}

	//Unnötige Stunden Entfernen
	foreach ($ar_ar_event2 as $key => $ar_event) {


		if (!isset($ar_ar_event2[$key]['SUMMARY'])) {
			$ar_ar_event2[$key]['SUMMARY'] = "???";
		}

		if (!isset($ar_ar_event2[$key]['LOCATION'])) {
			$ar_ar_event2[$key]['LOCATION'] = "???";
		}

		$teacher = explode(' ', $ar_ar_event2[$key]['DESCRIPTION']);

		if ($ar_ar_event2[$key]["SUMMARY"] == '+de' AND !in_array(end($teacher), $teacherIHaveInDe)) {
			unset($ar_ar_event2[$key]);
		} elseif ($ar_ar_event2[$key]["SUMMARY"] =='+en' AND !in_array(end($teacher), $teacherIHaveInEn)) {
			unset($ar_ar_event2[$key]);
		} elseif (in_array($ar_ar_event2[$key]["SUMMARY"], $classesIDontHave)) {
			unset($ar_ar_event2[$key]);
		} elseif (in_array(end($teacher), $teacherIDontHave)) {
			unset($ar_ar_event2[$key]);
		} else {

			$ar_ar_event2[$key]['SUMMARY'] .= ' - '.$ar_ar_event2[$key]['LOCATION'];
			$ar_ar_event2[$key]['LOCATION'] = 'BBS-Burgdorf';

			if ($ar_ar_event2[$key]['SUMMARY'] == '??? - ???') {
				$ar_ar_event2[$key]['SUMMARY'] = $ar_ar_event2[$key]['DESCRIPTION'];
				$ar_ar_event2[$key]['DESCRIPTION'] = '???';
			}
		}
	}

	//Gleichzeitige Stunden
	foreach ($ar_ar_event2 as $key1 => $event1) {
		foreach ($ar_ar_event2 as $key2 => $event2) {
			if ($event2["UID"] != $event1["UID"] AND $event2["DTSTART"] == $event1["DTSTART"] AND $event2["DTEND"] == $event1["DTEND"]) {		
				unset($ar_ar_event2[$key2]);
				break 2;
			}
		}
	}

	foreach ($ar_ar_event2 as $ar_event) {
		$uid_new_events[] =  $ar_event['UID'];
	}

	//von Gcal generierte Events ausschließen (nur von Untis nehmen)

	foreach (list_event($date)['items'] as $event) {

		if (strlen($event['iCalUID']) != 37) {

			if (!in_array($event['iCalUID'], $uid_new_events)) {

				pushbullet('q.tim.lai@gmail.com', 'note', 
					'Ausfall: '.$event['summary'], strftime('%a %e %R', strtotime($event['modelData']['start']['dateTime'])));

				delete_event($event['id']);

			} 
		}
	}

	foreach ($ar_ar_event2 as $arEvent) {

		add_event($arEvent['DTSTAMP'], $arEvent['DTSTART'], $arEvent['DTEND'], $arEvent['UID'], $arEvent['DESCRIPTION'], $arEvent['SUMMARY'], $arEvent['LOCATION']);

	}

}

untisToGCal(2);
untisToGCal(9);

$results = $batch->execute();

echo "3 \n";
print_r($results);