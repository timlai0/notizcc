<?php

/**
*
*/
class Sprit
{
	const HAENIGSEN = [52.48, 10.09];
	const UETZE = [52.46, 10.20];
	const OBERSHAGEN = [52.50,10.06];
	const BURGDORF = [52.435, 10.013];
	const FH = [52.321, 9.819];

	const KEYS = ['###'];

	static function preis($type, $town = self::HAENIGSEN) {

		date_default_timezone_set('Europe/Berlin');

		if (!in_array($type, ['e5', 'e10', 'diesel', 'all'], 1)) {
			die("Falscher Typ");
		}

		$filename = $type.$town[0].'-'.$town[1].'-cache.tmp';

		//Erstelle Datei falls nötig
		if (!file_exists($filename)) {

			fclose(fopen($filename, "w"));

		} else {
			//Datei existiert und Datum wird gelesen

			$cache = json_decode(file_get_contents($filename));

			if (!empty($cache)) {

				$t1 = new DateTime();
				$t1->setTimestamp(time());

				$t_cache = new DateTime();
				$t_cache->setTimestamp($cache[0]);

				//Datum ist nicht 10min her
				if (date_diff($t1 , $t_cache)->i < 10) {
					return $cache[1];
				}
			}
		}

		$nurOffen = 1;

		$output = '';

		$rad = 5;

		if ($type != 'all') {
			$sort = '&sort=price';
		} else {
			$sort = '';
		}

		$apikeys = self::KEYS;
		$lastApiKey = end($apikeys);

		foreach ($apikeys as $apikey) {

			$api = "https://creativecommons.tankerkoenig.de/json/list.php?lat=$town[0]&lng=$town[1]&rad=$rad$sort&type=$type&apikey=$apikey";

			$o_result = json_decode(file_get_contents($api));

			if (is_object($o_result) AND $o_result->ok) {
				break;
			}  elseif ($apikey == $lastApiKey) {
				http_response_code(500);
				print_r($o_result);
				die();
			}
		}

		$time = time();
		$type = ucfirst($type);

		$output_time = date("H:i", $time);

		$output .= "
		<ul class='collection with-header'>

		<li class='collection-header'>
		<h4>$type Benzin - $output_time</h4>
		</li>
		";

		foreach ($o_result->stations as $tankstelle) {

			if ($tankstelle->isOpen OR !$nurOffen) {

				if ($tankstelle->price == '') {
					continue;
				}

				$tankstelle->id;

				$tankstelle->dist;

				$tankstelle->houseNumber;
				$tankstelle->postCode;

				$tankstelle->name;

				if ($tankstelle->isOpen AND !$nurOffen) {
					$open = 'active';
				} else {
					$open = '';
				}

				if (empty($tankstelle->brand)) {
					$tankstelle->brand = "freie Tankstelle";
					$tsurl = urlencode($tankstelle->street.' '.$tankstelle->houseNumber.' '.$tankstelle->place);
				} else {
					$tsurl = urlencode($tankstelle->brand.' - '.$tankstelle->street.' '.$tankstelle->houseNumber.' - '.$tankstelle->place);
				}

				$output .= "
				<a class='collection-item $open' href='https://www.google.de/maps/search/$tsurl' target='_blank'><span class='new badge green' data-badge-caption='€'>$tankstelle->price</span>$tankstelle->brand - $tankstelle->street - $tankstelle->place</a>
				";
			}
		}

		$output .= "</ul>";


		$cache = json_encode([$time, $output]);

		file_put_contents($filename, $cache);


		return $output;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Benzin</title>
	<script src="../lib/js/jquery.min.js"></script>

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

	<!-- Compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<style type="text/css">

	.pointer {
		cursor: pointer
	}

</style>


<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">

</head>

<body>
	<div class="navbar">
		<nav>
			<div class="nav-wrapper black">

				<a class="brand-logo center">E5 Benzinpreis im 5 km Radius</a>

			</div>
		</nav>
	</div>

	<div class="row">
		<div class="col l1 hide-on-med-and-down">
		</div>

		<div class="col s12 m6 l5">
			<h2 class="center">Hänigsen</h2>

			<?php
			echo Sprit::preis('e5');
			?>

		</div>

		<hr class="hide-on-med-and-up">

		<div class="col s12 m6 l5">
			<h2 class="center">Leibniz FH</h2>

			<?php
			echo Sprit::preis('e5', Sprit::FH);
			?>

		</div>
		<div class="col l1 hide-on-med-and-down">
		</div>
	</div>
</body>
</html>
