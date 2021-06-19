<?php 
header("Content-Type: text/plain; charset=utf-8");
require_once "../config.php";
if (isset($_GET['f'])) {
	
	$note_name = $_GET['f'];
	if ($ar_note = db("SELECT `notes_content` FROM `notes` WHERE `notes_name` LIKE '$note_name'")) {
		echo urldecode($ar_note[0]['notes_content']);
	}
}