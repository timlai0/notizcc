<?php
require_once "../config.php";

class Webscan_HTML {
	static function add() {
		return '			<div class="card blue-grey darken-1">
		<div class="card-content white-text">
		<span class="card-title">Neu</span>

		<div class="input-field">
		<input type="text" id="title" name="title">
		<label for="title">Titel</label>

		</div>
		<div class="input-field">
		<input type="text" id="url" name="url">
		<label for="url">URL</label>				
		</div>
		<div class="input-field">
		<input type="text" id="element" name="element">
		<label for="element">Element</label>				
		</div>

		<p>
		<input name="type" class="filled-in" type="checkbox" id="type" />
		<label for="type">mit</label>
		</p>
		<div class="input-field">
		<input type="text" id="delimiter" name="delimiter">
		<label for="delimiter">delimiter</label>				
		</div>



		<div class="card-action">
		<button class="btn green" onclick="add() "type="submit" name="new">Hinzufügen</button>
		<button class="btn" onclick="add_test()" name="test">Test</button>
		<button class="btn red" onclick="toggle_add()" name="test">Abbrechen</button>
		<div id="test_result"></div>
		</div>

		</div>
		</div>';
	}

	static function lister() {

		$html_output = '<table class="lister">';
		$html_output .= '<tbody>';

		$script = '<script>$(function() {';

		$boxnr = 0;
		$boxtext = array();

		$ar_ar_items = Database::get_all();

		foreach ($ar_ar_items as $key => $row) {
			$ar_titles[$key] = $row['title'];
		}

		//Sortiert nach dem Titel
		array_multisort($ar_titles, SORT_ASC, $ar_ar_items);

		foreach ($ar_ar_items as $ar_c) {

			$html_output .= "<tr>";


			$id = $ar_c['id'];
			$title = $ar_c['title'];
			$url = $ar_c['url'];


			if ($ar_c['viewed']) {
				$viewed = '';
			} else {
				$viewed = 'green';
			}


			if ($ar_ar_changes = unserialize($ar_c['changes']) AND $ar_c['viewed'] == 0) {
				$rowspan = count($ar_ar_changes) + 1;
			} else {
				$rowspan = 1;
			}


			$html_output .= "<td rowspan='$rowspan' class='title'><a href='$url' target='_blank'><div class='full'>$title</div></a></td>";

			//falls das Element nicht ganz neu ist
			if (!empty($ar_c['changes'])) {

				//Wenn es ungelesene änderungen gibt, wird alles angezeigt
				if (!empty($viewed)) {

					

					foreach ($ar_ar_changes as $row) {

						$boxtext[] = base64_decode($row[1]);
						$html_output .= "<tr><td class='date $viewed center pointer' onclick='viewed($id)'>".date('d.m.Y H:i', $row[0]).'</td>';

						$tmp_text = base64_decode($row[1]);
						$html_output .= "<td><pre><code id='box-$boxnr' class='box'>$tmp_text</script></code></pre></td>";
						$html_output .= '</tr>';


						$script .= "$('#box-$boxnr').html(\"$tmp_text\"); ";

						$boxnr = $boxnr + 1;


					}

				} else {
					//Regulärer Output

					$tmp_text = $boxtext[] = base64_decode(end($ar_ar_changes)[1]);
					$html_output .= '<td class="c date center pointer" onclick="viewed('.$id.')">'.date('d.m.Y H:i', end($ar_ar_changes)[0]).'</td>';
					$html_output .= "<td><pre><code class='box' id='box-$boxnr'>$tmp_text</code></pre></td>";

					

					// $script .= "$('#box-$boxnr').html(\"$tmp_text\");";

					$boxnr = $boxnr + 1;

				}


			} else {			//Bei neuen Elementen, die Zeile schließen
				$html_output .= '<td class="date">-</td><td></td></tr>';

			}

		}

		$html_output .= '</tbody></table>';

		$last_update = date('d.m.Y H:i', db("SELECT `value` FROM `webscan_settings` WHERE `id` = 1")[0]['value']);

		$script .= "$('#last_change').html('$last_update')";
		$script .= '})</script>';

		return $html_output.$script;
	}

}

class Scan {
	static function test($url, $element, $delimiter, $type) {

		if (empty($delimiter)) {
			$delimiter = '</';
		}

		if (@$page = file_get_contents($url) AND $page == true) {

			if ($result = strstr($page, $element)) {

				if ($type == 'true') {
					#mit
					return (array('success' => 1, 'msg' => htmlentities(explode($delimiter, $result, 2)[0])));

				} else {
					return (array('success' => 1, 'msg' => explode($delimiter, explode($element, $result, 2)[1])[0]));
				}

			} else {

				return (array('success' => 'E1', 'msg' => 'Element nicht gefunden'));
				die();
			}
		} else {
			return (array('success' => 'E2', 'msg' => 'URL nicht gefunden'));
		}
	}

	static function curlLoad($url, $cookie) {

		$options = array(
		CURLOPT_RETURNTRANSFER => true,     // return web page
		CURLOPT_HEADER         => true,     //return headers in addition to content
		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		CURLOPT_ENCODING       => "",       // handle all encodings
		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
		CURLOPT_TIMEOUT        => 120,      // timeout on response
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		CURLINFO_HEADER_OUT    => true,
		CURLOPT_SSL_VERIFYPEER => true,     // Validate SSL Certificates
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_COOKIE         => $cookie
		);

		$ch      = curl_init($url);
		curl_setopt_array($ch, $options);
		$rough_content = curl_exec($ch);

		$err     = curl_errno($ch);
		$errmsg  = curl_error($ch);
		$header  = curl_getinfo($ch);
		
		curl_close($ch);

		$header_content = substr($rough_content, 0, $header['header_size']);
		$body_content = trim(str_replace($header_content, '', $rough_content));
		$pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m"; 
		preg_match_all($pattern, $header_content, $matches); 
		$cookiesOut = implode("; ", $matches['cookie']);

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['headers']  = $header_content;
		$header['cookies'] = $cookiesOut;
		$header['content'] = $body_content;

		return $header['content'];
	}

	static function check_all() {
		$ar_id = db("SELECT `id` FROM `webscan_pages`");

		if (count($ar_id) > 0 ) {

			$results = array();
			foreach ($ar_id as $id) {
				$results[] = array($id, Scan::check($id['id']));
			}

			return json_encode($results);
		} else {
			return json_encode(array('success' => '1', 'msg' => "nichts zu Prüfen"));			
		}
	}

	static function check($id = 1) {

		if ($ar_result = db("SELECT * FROM `webscan_pages` WHERE `id` = $id")[0]) {
			$test_result = Scan::test($ar_result['url'], $ar_result['element'], $ar_result['delimiter'], $ar_result['type']);
			$json_test_result = $test_result;

			if ($json_test_result['success'] != 1) {
				return $test_result;
				die();
			};


			if (!empty($ar_result['changes']) AND is_array($ar_ar_change = unserialize($ar_result['changes']))) {	


				if (base64_decode(end($ar_ar_change)[1]) != $json_test_result['msg']) {

					$mail = '
					<a href="'.$ar_result['url'].'" class="btn" target="_blank"><h1>'.$ar_result['title'].'</h1></a>
					<table>
					<tr>
					<td style="border: 1px solid black">Vorher</td>
					<td style="border: 1px solid black">Jetzt</td>
					</tr>
					<tr>
					<td style="border: 1px solid black"><code><pre>'.base64_decode(end($ar_ar_change)[1]).'</pre></code></td>
					<td style="border: 1px solid black"><code><pre>'.$json_test_result['msg'].'</pre></code></td>
					</tr>
					</table>
					<a href="https://sklb.cf/" class="btn" target="_blank">WebScan</a>';

					Mail::send('Change auf "'.$ar_result['title'].'"', $mail);

					array_push($ar_ar_change, array(time(), base64_encode($json_test_result['msg'])));
					$msg = "CHANGE";

					$s_ar_ar_change = serialize($ar_ar_change);
					db("UPDATE `webscan_pages` SET `changes` = '$s_ar_ar_change ', `viewed` = '0' WHERE `webscan_pages`.`id` = $id;");
					db("UPDATE `webscan_settings` SET `value` = UNIX_TIMESTAMP() WHERE `webscan_settings`.`id` = 2;");
				} else {
					$msg = 'kein Change seit '.date('d.m.Y H:i', end($ar_ar_change)[0]);
				}
			} else {
				$s_ar_ar_change = serialize(array(array(time(), base64_encode($json_test_result['msg']))));

				db("UPDATE `webscan_pages` SET `changes` = '$s_ar_ar_change' WHERE `webscan_pages`.`id` = $id;");
				$msg = 'neu hinzugefügt';
			}

		} else {
			$msg = 'datenbank';
		}

		db("UPDATE `webscan_settings` SET `value` = UNIX_TIMESTAMP() WHERE `webscan_settings`.`id` = 1");

		return (array('success' => 1, 'msg' => $msg));
	}
}



class Database {


	static function add($title, $url, $element, $delimiter, $type) {

		if (empty($delimiter)) {
			$delimiter = '</';
		}

		Database::get_all();

		$ar_entries = Database::get_all();

		foreach ($ar_entries as $entry) {
			if ($entry['title'] == $title) {
				return array('type' => 'E3', 'msg' => 'Doppelter Titel');
				die();
			}
		}

		$test_result = Scan::test($url, $element, $delimiter, $type);

		if ($test_result['success'] != 1) {
			return $test_result;
			die();
		};

		$title = esc($title);
		$url = esc($url);
		$element = esc($element);
		$delimiter = esc($delimiter);
		$type = esc($type);


		if (db("INSERT INTO `webscan_pages` (`title`, `url`, `element`, `delimiter`, `type`) VALUES ('$title', '$url', '$element', '$delimiter', '$type')")) {
			return array('success' => 1, 'msg' => 'Erfolgreich');
		}
	}

	static function get_all() {
		return db("SELECT * FROM `webscan_pages`", 1);
	}

	static function viewed($id) {

		$tmp = !db("SELECT `viewed` FROM `webscan_pages` WHERE `id` = $id")[0]['viewed'];
		echo $tmp;
		return db("UPDATE `webscan_pages` SET `viewed` = '$tmp' WHERE `webscan_pages`.`id` = $id;");
		
	}

}

class Mail {
	static function send($title, $body) {

		$api_user = "api:key-###";

		$post_data['to'] = "webscan@timlai.de";
		$post_data['from'] = "WebScan <webscan@notiz.cc>";
		$post_data['subject'] = $title;
		$post_data['html'] = $body;


		foreach ($post_data as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}

		$post_string = implode ('&', $post_items);

		$curl_connection = curl_init('https://api.eu.mailgun.net/v3/mg.notiz.cc/messages');

		curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);

		curl_setopt($curl_connection, CURLOPT_USERPWD, $api_user);

		curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);

		$result = curl_exec($curl_connection);

		if (curl_errno($curl_connection)) {
			print_r(curl_getinfo($curl_connection));
			echo curl_error($curl_connection);
			die();
		}

		curl_close($curl_connection);
	}
}


?>