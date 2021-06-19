<?php
require_once "../config.php";


?>

<!DOCTYPE HTML>
<html lang="de">

<head>
	<!-- Meta Stuff -->
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>MD-HTML von notiz.cf/<?php echo $note_name ?></title>

	<!-- Meta Stuff -->
	<meta property="og:title" content="notiz.cf | <?php echo $note_name ?>" />
	<meta property="og:description" content="HTML von notiz.cf/<?php echo $note_name ?>" />
	<meta property="og:site_name" content="notiz.cf | <?php echo $note_name ?>" />
	<meta property="og:url" content="http://notiz.cf" />
	<meta property="og:image" content="http://notiz.cf/img/512.png" />
	<meta name="keywords" content="notes,notiz.cf,notiz,url">
	<meta name="theme-color" content="#000">

	<link rel="shortcut icon" href="../favicon.ico">

	<!-- JS -->
	<!-- jquery -->
	<script src="../lib/js/jquery.min.js"></script>
	<script src="../lib/js/jquery.textarea.js"></script>

	<!-- Materialize.CSS -->
	<link rel="stylesheet" href="../lib/css/materialize.min.css">
	<script src="../lib/js/materialize.min.js"></script>

	<!-- highlight.js -->
	<link rel="stylesheet" href="../lib/css/highlight.min.css">
	<script src="../lib/js/highlight.min.js"></script>
	<script>
		hljs.initHighlightingOnLoad();
	</script>

	<!-- Clipboard.js -->
	<script src="../lib/js/clipboard.min.js"></script>

	<script>
		if (location.protocol != 'https:') {
			location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
		}
	</script>

	<style>
		html {
			background-color: #666;
		}

		body {
			margin: 1vw;
			padding: 1vw;
			background-color: #fff;
			border: 1px solid black;
			line-break: auto;

		}

		ul li,
		ol li {
			margin-left: 1.5em;
		}

		ul li {
			list-style: square;
		}
	</style>
</head>

<body class="mdonly">
	<?php
	if (isset($_GET['f'])) {
		echo Html::markdown($_GET['f']);
	} ?>

</body>

</html>