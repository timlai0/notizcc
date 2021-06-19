<?php 

class User {
	static function sd() {
		@session_start();
		if (session_destroy()) {
			die('Session gelöscht');
		} else {
			die('FEHLER: Session wurde nicht gelöscht!');
		}
	}

	static function logout() {
		unset($_SESSION['admin']);
		unset($_SESSION['user']);
		unset($_SESSION['userId']);
		die('1');
	}
	
	static function getuser($user) {
		global $dbCon;

		if ($user == 0) {
			return "";
		} elseif ($db_result = db("SELECT `user_name` FROM `user` WHERE `user_id` = $user")) { 
			return $db_result[0]['user_name'];
		}
	}
	
	
	static function html_list() {

		if (empty($_SESSION['userId']) OR empty($_SESSION['user']) OR $_SESSION['userId'] == 0) {
			die("nicht eingelogt");
		}

		$userId = $_SESSION['userId'];
		$username = $_SESSION['user'];

		$ar_notes = db("SELECT * FROM `notes` WHERE `notes_creator` = $userId ORDER BY `notes_lastchange` DESC");

		echo '<ul class="collection with-header">
		<li class="collection-header"><h4>';
		echo "<h1>notiz.cf</h1><h4>Angemeldet als <b>$username</b></h4></h4><a class='red pointer btn' onclick='logOut()'>LogOut</a></li>";

		foreach ($ar_notes as $p) {

			echo '<a class="collection-item" href="../'.$p["notes_name"].'" target="_blank">
			<div>';

			$text = htmlentities(urldecode($p['notes_content']));

			if (!empty(explode('# ', strtok($text, "\n"))[1])) {
				echo '<b>'.explode('# ', strtok($text, "\n"))[1].'</b>';
			}		else {
				echo $p["notes_name"];
			}

			if (!empty($p['notes_password'])) {
				echo '<li class="secondary-content"><i class="material-icons">lock</i>';
			}
			echo '</div></a>';
		}
	}
}

class Admin {

	
	static function html_list() {
		$ar_notes = db("SELECT * FROM `notes` ORDER BY `notes_lastchange` DESC");
		echo '<table class="admin-liste">';
		foreach($ar_notes as $p){
			echo '<tr>
			<td ';
			if (!empty($p['notes_password'])) {
				echo ' class="yellow"';
			}		

			echo '>
			<a href="../'.$p["notes_name"].'" target="_blank">
			<div>'.$p["notes_name"].'</div>
			</a>
			</td>

			<td class="preview">
			<div>';

			echo substr(htmlentities(urldecode($p["notes_content"])), 0, 300);
			if (strlen(htmlentities(urldecode($p["notes_content"]))) > 300) {
				echo " <span class='red-text bold'>...</span>";
			}

			echo '</div>
			</td>	
			<td class="ip">';

			if (!empty($p["HTTP_X_FORWARDED_FOR"])) {
				echo "<a href='https://myip.ms/info/whois/".explode(', ', $p["HTTP_X_FORWARDED_FOR"])[0]."' target=_blank>".explode(', ', $p["HTTP_X_FORWARDED_FOR"])[0]."</a>";
			};

			echo '</td><td>
			<div>';

			echo User::getuser($p['notes_lastchanger']);

			echo '</div>
			</td>

			<td class="txt_btn">
			<a href="../txt/'.$p["notes_name"].'" target="_blank">
			<div>TXT</div>
			</a>
			</td>							
			<td class="del">
			<a onclick=deleteNote("'.$p["notes_name"].'") class="pointer center">
			<div>
			<i class="material-icons">&#xE92B;</i>
			</div>
			</a>
			</td>		
			</tr>';
		}  
		echo '</table>';
	}
}

class Html {
	static function text_markdown($txt) {
		include_once "lib/php/parsedown.php";
		return $Parsedown->setBreaksEnabled(true)->text($txt);
	}

	static function markdown($note_name) {
		if ($note = db("SELECT * FROM `notes` WHERE `notes_name` = '$note_name'")) {
			return Html::text_markdown(urldecode($note[0]['notes_content']));
		}
	}
}


class Note {

	static function delete($note_name) {
		@session_start();
		//Überprüfen ob jmd. eingelogt ist
		if (!empty($_SESSION['userId'])) {
			$user_id = $_SESSION['userId'];
		} else {
			die("nicht eingelogt");
		}

		//Ersteller laden
		$ar_note = db("SELECT `notes_creator`, `notes_lastchanger` FROM `notes` WHERE `notes_name` = '$note_name'")[0];

		//Überprüfen, ob der Ersteller oder ein Admit die Notiz löschen will
		if (($ar_note['notes_creator'] == $user_id AND $ar_note['notes_lastchanger'] == $user_id) OR isset($_SESSION['admin']) AND ($_SESSION['admin'] == 1)) {

			if (db("DELETE FROM `notes` WHERE `notes_name` = '$note_name'")) {
				return true;
			} else {
				die("Besitzer hat sich geändert");
			}
		}
	}


	static function set_password($note_name, $note_password_set) {
		@session_start();
		//Passwort setzen	
		
		if (db("SELECT * FROM `notes` WHERE `notes_name` = '$note_name' LIMIT 0 , 1")) {

			$ar_note = db("SELECT * FROM `notes` WHERE `notes_name` = '$note_name' LIMIT 0 , 1");
			
			if (empty($ar_note[0]['notes_password'])) {
				if (db("UPDATE `notes` SET `notes_password` = '$note_password_set' WHERE `notes_name` = '$note_name'")) {
					
						//session access setzen
					if (isset($_SESSION['access'])) {
						if ($_SESSION['access'][0] != $note_name) {
							array_push($_SESSION['access'], $note_name);
						}
					} else {
						$_SESSION['access'] = array($note_name);
					}
					return 1;
				}
			} else {
				return 2;
			}
		} else {
			return 'Leere Notiz kann kein Passwort haben';
		}
	}
}

?>