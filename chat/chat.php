<?php

require "../config.php"	;
require "../lib/php/parsedown.php";

class Chat {

	static function display() {

		$md = new Parsedown;

		$ar_ar_chattext = db('SELECT * FROM `chat`');

		$i = 1;

		$result['html'] = '';

		foreach ($ar_ar_chattext as $ar_chattext) {

			$result['html'] .= '<div class="row chatmsg" style="color:#'.$ar_chattext['color'].'">';

			$result['html'] .= '<div class="col s4 m3 l1 user">'.$ar_chattext['user'].'</div><div class="col s4 l1 time">'.date('d.m.Y H:i', $ar_chattext['time']).'</div><div class="msg col s12 m12 l10">'.$md->setBreaksEnabled(true)->text($ar_chattext['msg']).'</div></div>';

			$i++;

		}

		$result['lastmsg'] = end($ar_ar_chattext);
		
		return json_encode($result);
	}

	static function chatInput($user, $color, $text) {

		$user = esc($user);
		$color = esc($color);
		$text = esc($text);

		if (!empty($_SESSION['admin'])) {
			$user = '[Admin] '.$_SESSION['user']; 
		} elseif (!empty($_SESSION['user'])) {
			$user = '[User] '.$_SESSION['user'];
		} else {
			$user = '[Gast] '.$user;
		}

		return db("INSERT INTO `chat` (`user`, `color`, `msg`, `time`) VALUES ('$user', '$color', '$text', UNIX_TIMESTAMP())", 1);
	}


	static function loginBtn() {
		if (empty($_SESSION['user'])) {
			echo '<div class="col s2"><a href="../login" class="btn">Login</a></div>';
		} else {
			echo '<div class="col s2"><a href="user" target="_blank">Username: '.$user.'</a></div>';

		}
	}
}

?>