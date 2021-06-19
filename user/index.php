<?php
require_once("../config.php");

if (isset($_SESSION['user'])) {
	$userid = $_SESSION['userId'];
	$username = $_SESSION['user'];
} elseif (!empty($_SESSION['history'][0])) {
	header("Location: ../" . $_SESSION['history'][0]);
} else {
	header("Location: ../login");
}

if (!empty($_SESSION['admin'])) {
	header("Location: admin.php");
	die();
}

if (!isset($_SESSION['history'][0])) {
	$_SESSION['history'][0] = '';
}

?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<title>notiz.cc <?php echo "$username" ?></title>

	<meta property="og:title" content="notiz.cc" />
	<meta property="og:site_name" content="notiz.cc" />
	<meta property="og:url" content="http://notiz.cc" />
	<meta property="og:image" content="favicon.ico" />
	<link rel="manifest" href="/admin/manifest.json">
	<meta name="theme-color" content="#000000">
	<link rel="shortcut icon" href="../favicon.ico">

	<script src="../lib/js/jquery.min.js"></script>

	<!-- Raven -->
	<script src="https://cdn.ravenjs.com/3.8.0/raven.min.js"></script>
	<script>
		Raven.config('https://15da66d440c144ddb5a5b9ff6b2b7042@sentry.io/112192').install()
	</script>

	<!-- Materialize.CSS -->
	<link rel="stylesheet" href="../lib/css/materialize.min.css">
	<script src="../lib/js/materialize.min.js"></script>
	<link rel="stylesheet" href="../lib/css/icon.css">

	<link rel="stylesheet" href="user.css">

	<script type="text/javascript">
		if (location.protocol != 'https:') {
			location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
		}

		function logOut() {
			$.post("../login/usercontrol.php", {
					logOut: Math.random()
				},
				function(data) {
					if (data == 1) {
						window.location.replace("../<?php echo $_SESSION['history'][0] ?>#lo");
					}
				});

		}

		<?php require_once("user.js.php") ?>;
	</script>

<body>
	<!--header-bar-->
	<nav>
		<div class="navbar-fixed">
			<div class="nav-wrapper black">
				<div class="input-field">
					<input id="search" type="search" name="gotonote" required pattern="^[a-zA-Z0-9\-]+$">
					<label for="search"><i class="material-icons">&#xE8B6;</i></label>
					<i class="material-icons">close</i>
				</div>
			</div>
		</div>
	</nav>

	<main>
		<div class="container">

			<div class="liste">
				<?php
				User::html_list();
				?>
			</div>

		</div>
	</main>

	<footer class="page-footer black">
		<div class="container grey-text">
			<div class="row">&copy; timlai_ 2016</div>
		</div>
	</footer>

	<body>

</html>