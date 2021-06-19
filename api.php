<?php
require_once "config.php";

if (isset($_POST['note_password_set']) && isset($_POST['note_name'])) {
	echo Note::set_password($_POST['note_name'], generate_hash($_POST['note_password_set']));
	die();
}

if (isset($_POST['note_name']) && isset($_POST['note_newcontent']) && isset($_POST['change'])) {
	//note speichern
	//Eingang verarbeiten
	$note_name = $_POST['note_name'];
	$pre_change = $_POST['change'];
	
	$note_content = $_POST['note_newcontent'];

	if (isset($_SESSION['userId'])) {
		$user_id = $_SESSION['userId'];
	} else {
		$user_id = 0;
	}
	
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$forwarded_for = "";
	}
	
	//notiz mit dem Namen suchen

	@$ar_note = db("SELECT * FROM  `notes` WHERE `notes_name` = '$note_name'")[0];
	
	if (!$ar_note) {
	//neuer Note
		if ($result = db("INSERT INTO `notes` (`notes_name`, `notes_content`, `notes_lastchanger`, `notes_creator`, `notes_adminonly`, `notes_change`) VALUES ('$note_name', '$note_content', '$user_id', '$user_id', '0', '1');", 1)) {
			die("Neue Notiz erstellt");
		} else { 
			die('DB ERROR: '.$result);
		}
	}	
	
	//Access hat man: beim Erstellter und nach PW Eingabe und bei keinem Passwort
	if (($user_id == $ar_note['notes_creator'] AND $user_id != 0) OR (!empty($_SESSION['access']) AND in_array($note_name, $_SESSION['access'])) OR empty($ar_note['notes_password']) OR !empty($_SESSION['admin'])) {
		$access = 1;
	} else {
		$access = 0;
	}

	//Wenn Leer dann löschen
	if (empty($_POST['note_newcontent']) AND $access == 1) {
		
		db("DELETE FROM `notes` WHERE `notes`.`notes_name` = '$note_name'");
		
		if ($ar_note['notes_password']) {
			die('Notiz mit Passwort gelöscht');
		} else {
			die('Notiz gelöscht');
		}
	}

	//schrieben
	if ($access == 1) {
		if (empty($ar_note['notes_adminonly'])) {
			
			$change = $ar_note['notes_change'] + 1;
			
			if ($result = db("UPDATE `notes` SET `HTTP_X_FORWARDED_FOR` = '$forwarded_for', `notes_content` =  '$note_content', `notes_lastchanger` =  '$user_id', `notes_change` = '$change' WHERE `notes_name` = '$note_name'", 1)) {
				die();
			} else { 
				die($result);
			}
		} else {
			die('Nur für Admins');
		}
	} else {
		die('Kein Zugriff! Seite bitte neuladen');
	}
}

if (isset($_POST['note_name']) && isset($_POST['note_refresh'])) {
	//note txt auslesen
	$note_name = $_POST['note_name'];
	if ($ar_note = db("SELECT * FROM  `notes` WHERE  `notes_name` = '$note_name' LIMIT 0 , 1")) {

		$ar_ans = array(
			$ar_note[0]['notes_change'],
			$ar_note[0]['notes_lastchange'],
			$ar_note[0]['notes_content'],
			Html::text_markdown(urldecode($ar_note[0]['notes_content']))
		);

		die(json_encode($ar_ans));
	} else {
		die(json_encode(array(0, "nie", "", "")));	
	}
	http_response_code(500);
}

if (isset($_POST['note_password']) AND isset($_POST['note_name_pw'])) {
	//Passwort von Notizen prüfen
	$note_name = $_POST['note_name_pw'];
	
	$ar_note = db("SELECT `notes_password` FROM  `notes` WHERE  `notes_name` = '$note_name' LIMIT 0 , 1");
	if (validate_pw($_POST['note_password'], $ar_note[0]["notes_password"])) {
		
		if (isset($_SESSION['access'])) {
			$_SESSION['access'][] = $note_name;
		} else {
			$_SESSION['access'] = array($note_name);
		}
		
		header('Location:'. $note_name);
		die();

	} else {
		header('Location:'.$note_name.'#wpw');
		die();
	}
	die("Fehler?");
}

if (isset($_POST['deleteNote']) AND isset($_POST['note_name'])) {
	if (Note::delete($_POST['note_name']) AND $_SESSION['admin']) {
		Admin::html_list();
	} else {
		User::html_list();
	}
	die();
}

//ausloggen ohne access zu verlieren
if (isset($_POST['logOut'])) {
	User::logout();
}

//Session Löschen
if (isset($_POST['delSession'])) {
	User::sd();
}


http_response_code(400);
die("request error");

?>