<?php
require_once "../config.php";

if (isset($_GET['f'])) {

	$note_name = $_GET['f'];
	if ($ar_note = db("SELECT `notes_content` FROM `notes` WHERE `notes_name` LIKE '$note_name'")) {
		$note_content = urldecode($ar_note[0]['notes_content']);
	} else {
		die();
	}
}
?>

<!DOCTYPE HTML>
<html lang="de">

<head>
	<!-- Meta Stuff -->
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>"HTML von notiz.cf/<?php echo $note_name ?>"</title>

	<!-- Meta Stuff -->
	<meta property="og:title" content="notiz.cf | <?php echo $note_name ?>" />
	<meta property="og:description" content="HTML von notiz.cf/<?php echo $note_name ?>" />
	<meta property="og:site_name" content="notiz.cf | <?php echo $note_name ?>" />
	<meta property="og:url" content="http://notiz.cf" />
	<meta property="og:image" content="http://notiz.cf/img/512.png" />
	<meta name="keywords" content="notes,notiz.cf,notiz,url">
	<meta name="theme-color" content="#000">

	<script>
		if (location.protocol != 'https:') {
			location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
		}
	</script>

	<link rel="shortcut icon" href="../favicon.ico">

	<style>
		* {
			font-family: "Roboto"
		}
	</style>

</head>

<body>

	<?php
	echo $note_content;
	?>
</body>

</html>