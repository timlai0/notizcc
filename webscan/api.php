<?php

require_once 'php.php';

if (!empty($_POST)) {

	if (!empty($_POST['show_add'])) {
		echo Webscan_HTML::add();
	}

	if (!empty($_POST['lister'])) {
			// echo json_encode(Database::lister());
		echo Webscan_HTML::lister();
	}

	if (!empty($_POST['add_test'])) {

		$url = addslashes($_POST['url']);
		$element = $_POST['element'];
		$delimiter = $_POST['delimiter'];

		$type = $_POST['type'];

		echo json_encode(Scan::test($url, $element, $delimiter, $type));
	}

	if (!empty($_POST['add'])) {

		$title = addslashes($_POST['title']);
		$url = addslashes($_POST['url']);
		$element = $_POST['element'];
		$delimiter = $_POST['delimiter'];
		$type = addslashes($_POST['type']);

		echo json_encode(Database::add($title, $url, $element, $delimiter, $type));

	}

	if (!empty($_POST['viewed'])) {
		$id = $_POST['viewed'];

		echo json_encode(Database::viewed($id));
	}

	if (isset($_POST['html_check'])) {
		Scan::check_all();
		echo Webscan_HTML::lister();


	} 
} elseif (isset($_GET['ck'])) {
	echo Scan::check_all();
} else {

	ob_end_clean();
	header("Connection: close");
	ignore_user_abort(true); // just to be safe
	ob_start();
	$size = ob_get_length();
	header("Content-Length: $size");
	ob_end_flush(); // Strange behaviour, will not work
	flush(); // Unless both are called !
	// Do processing here 

	Scan::check_all();
}

?>