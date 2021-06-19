<?php

session_start();

if (!empty($_SESSION['admin'])) {
	header("Location: ../user/admin.php");
	die();
}

if (isset($_SESSION['user'])) {
	header("Location: ../user");
	die();
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="../lib/js/jquery.min.js"></script>

	<!-- Meta Stuff -->
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">

	<title>notiz.cc | Login</title>

	<meta property="og:url" content="http://notiz.cc" />
	<meta property="og:image" content="http://notiz.cc/lib/img/512.png" />
	<meta name="theme-color" content="#000">

	<link rel="shortcut icon" href="../favicon.ico">

	<link rel="stylesheet" href="../lib/css/icon.css">

	<!-- Materialize.CSS -->
	<link rel="stylesheet" href="../lib/css/materialize.min.css">
	<script src="../lib/js/materialize.min.js"></script>

	<!-- Raven -->
	<script src="https://cdn.ravenjs.com/3.8.0/raven.min.js"></script>
	<script>
		Raven.config('https://15da66d440c144ddb5a5b9ff6b2b7042@sentry.io/112192').install()
	</script>

	<script>
		if (location.protocol != 'https:') {
			location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
		}

		$(function() {
			if (location.hash == "#wpw") {
				Materialize.toast("User mit dem Passwort nicht gefunden", 8000);
				history.pushState('', document.title, window.location.pathname);
			}

			if (location.hash == "#reged") {
				Materialize.toast("Erfolgreich registriert", 8000);
				history.pushState('', document.title, window.location.pathname);
			}
		})
	</script>

	<!-- CSS -->
	<link rel="stylesheet" href="../css.css">
	<link rel="stylesheet" href="css_login.css">

</head>

<body class="login">
	<nav>
		<a href="../<?php echo $_SESSION['history'][0] ?>">
			<div class="nav-wrapper black">
				<span class="brand-logo">notiz.cc</span>
			</div>
		</a>
	</nav>

	<div class="card black logininterface">
		<div class="card-content white-text">
			<h4 class="center-align truncate">Login</h4>
		</div>
		<div class="card-action">
			<div class="input-field white-text">
				<form action="usercontrol.php" method="POST">
					<div class="row">
						<div class="input-field">
							<input name="user_login" id="user" type="text" class="validate" autofocus>
							<label for="user">Username</label>
						</div>
					</div>

					<div class="row">
						<div class="input-field">
							<input name="user_pw" id="password" type="password" class="validate">
							<label for="password">Passwort</label>
						</div>
					</div>
					<div class="row">
						<button class="btn green submitbutton" type="submit">Anmelden</button>
					</div>

				</form>
			</div>
		</div>
		<a href="reg.php">
			<div class="card-content white-text center border-bot"><button class="btn submitbutton orange">Registrieren</button></div>
		</a>
		<a href="../<?php echo $_SESSION['history'][0] ?>" class="pointer">
			<div class="card-content white-text">
				<p class="center"><i class="material-icons">arrow_back</i></p>
			</div>
		</a>
	</div>
</body>

</html>