<?php

session_start();

if (!empty($_SESSION['user'])) {
	unset($_SESSION['user']);
	unset($_SESSION['admin']);	
}
?>
<!DOCTYPE html>
<html>
	<head>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>

		<!-- Meta Stuff -->
		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">

		<title>notiz.cf | admin</title>

		<meta property="og:url" content="http://notiz.cf" />
		<meta property="og:image" content="http://notiz.cf/lib/img/512.png" />
		<meta name="theme-color" content="#000">

		<link rel="shortcut icon" href="favicon.ico">

		<!--Import Google Icon Font-->
		<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">		

		<!-- Materialize.CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

		<script>
			$(function() {
				if (location.hash == "#falschwdh") {
					Materialize.toast("Passwort wurde falsch wiederholt", 8000);
					history.pushState('', document.title, window.location.pathname);
				}	
				if (location.hash == "#uservergeben") {
					Materialize.toast("Username schon vergeben", 8000);
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
			<a href="../<?php echo $_SESSION['history'][0]?>">
				<div class="nav-wrapper black">
					<span class="brand-logo">notiz.cf</span>
				</div>
			</a>
		</nav>
		
		<div class="card black logininterface">
			<div class="card-content white-text">
				<h4 class="center-align truncate">Registrieren</h4>
			</div>
			<div class="card-action">
				<div class="input-field white-text">
					<form action="usercontrol.php" method="POST">
						<div class="row">
							<div class="input-field">
								<input name="user_reg" id="user" type="text" autofocus maxlength="32" required>
								<label for="user">Username</label>
							</div>
						</div>
		
						<div class="row">		
							<div class="input-field">
								<input name="user_pw" id="password" type="password" required>
								<label for="password">Passwort</label>
							</div>
						</div>
						<div class="row">		
							<div class="input-field">
								<input name="user_pw2" id="password2" type="password" required>
								<label for="password2">Passwort wiederholen</label>
							</div>
						</div>						
						<div class="row">
							<button class="btn green submitbutton" type="submit">Registrieren</button>
						</div>
					</form>
				</div>
			</div>
			<a href="index.php">
				<div class="card-content white-text center border-bot"><button class="btn submitbutton orange">Anmelden</button></div>
			</a>
			<a href="../<?php echo $_SESSION['history'][0]?>" class="pointer"><div class="card-content white-text">
				<p class="center"><i class="material-icons">arrow_back</i></p>
			</div></a>						
		</div>
	</body>
</html>