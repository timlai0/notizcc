<?php

require_once 'chat.php';
// API-Teil

	if (!empty($_POST['color']) AND !empty($_POST['message'])) {


		if (empty($_POST['user'])) {
			$user = '';
		} else {
			$user = $_POST['user'];
		}

		$text = rtrim($_POST['message']);

		if (!empty($text)) {
			Chat::chatInput(substr($user, 0, 18), $_POST['color'], $text);
			echo Chat::display();
			die();

		} else {

			echo Chat::display();
			die();
		
		}

	}

	if (!empty($_POST['view'])) { 
		echo Chat::display();
	}

?>