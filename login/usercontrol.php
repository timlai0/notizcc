<?php

include_once "../config.php";

if (isset($_POST['user_pw']) AND isset($_POST['user_login'])) {
		//Login Passwort pruefen
	$user_login = $_POST['user_login'];
	
	$ar_user = db("SELECT * FROM `user` WHERE `user_name` = '$user_login'");

	if (!empty($ar_user) AND validate_pw($_POST['user_pw'], $ar_user[0]["user_pass"])) {
		
		$_SESSION['user'] = $ar_user[0]["user_name"];
		$_SESSION['userId'] = $ar_user[0]["user_id"];
		
		
		if ($ar_user[0]["user_admin"] == 1) {
			$_SESSION['admin'] = 1;
			header('Location: ../user/admin.php');	
			die();
		}		

		if ($_SESSION['history'][0] == "ccna") {
			header('Location: ../ccna');				
			die();
		}

		header('Location: ../user');				
		die();

	} else {
		header('Location: ../login#wpw');
		die();
	}
}

if (!empty($_POST['user_pw']) AND !empty($_POST['user_reg']) AND !empty($_POST['user_pw2'])) {
	$user_reg = $_POST['user_reg'];

	if (strlen($user_reg) > 32) {
		header('Location: reg.php');			
		die();
	}
	
	if ($_POST['user_pw'] != $_POST['user_pw2']) {
		header('Location: reg.php#falschwdh');
		die();
	}
	
	
	if ($ar_user = db("SELECT * FROM `user` WHERE `user_name` = '$user_reg'")) {
		header('Location: reg.php#uservergeben');			
		die();
	}
	
	$pwhash = generate_hash($_POST['user_pw']);
	
	db("INSERT INTO `user` (`user_id`, `user_name`, `user_pass`, `regDate`) 
		VALUES (NULL, '$user_reg', '$pwhash', CURRENT_DATE());");
	header('Location: ../login#reged');
	die();
	
}

http_response_code(400);
die("request error");