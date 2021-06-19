<?php

session_start();
if (!empty($_POST['username']) AND !empty($_POST['password'])) {

	$hash = '$2y$11$I4nc4PNnc8r1Kv84QUFI9.VHPgf3wic4D9dZBXvOmm7/bgHToIXbK';
	if ($_POST['username'] == "admin" AND crypt($_POST['password'], $hash)==$hash) {
		$_SESSION['user'] = 'admin';
		$_SESSION['admin'] = 1;
		$_SESSION['userId'] = 1;

		require 'main.php';		
		die();
	}
} elseif (isset($_SESSION['admin']) AND $_SESSION['admin'] == 1) {
	require 'main.php';	
	die();
}

?>



<!DOCTYPE html>
<html>
<head>
	<title>WebScan Login</title>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="../lib/css/materialize.min.css"  media="screen,projection"/>

	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="../lib/js/materialize.min.js"></script>
	
	<script type="text/javascript" src="script.js"></script>
	<style type="text/css">

	body {
		overflow-y: scroll;
		background-color: #1C1C1C
	}

</style>
</head>

<body>
	<nav class="grey row">
		<div class="col s1"></div>
		<div class="col s10 nav-wrapper">
			<a href="./" class="brand-logo">WebScan</a>
		</div>


		<div class="col s1"></div>
	</nav>


	<div class="row">
		<div class="col s0 m1"></div>

		<div class="col s12 m10 card grey darken-3">
			<form method="POST">
				<div class="card-content white-text">
					<div class="input-field">
						<input type="text" id="username" name="username" autofocus>
						<label for="username">username</label>
					</div>
					<div class="input-field">
						<input type="password" id="password" name="password">
						<label for="password">password</label>
					</div>

					<div class="card-action">
						<button class="btn black" type="submit">Login</button>
					</div>
				</div>
			</form>
		</div>

		<div class="col s0 m1"></div>

	</div>

</body>
</html>