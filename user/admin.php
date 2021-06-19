<?php	
require_once '../config.php';

	if (empty($_SESSION['admin']))  {
		header("Location: ../");
		die();
	}
	
	//read out the notes
	
	if (!empty($_POST['killAll'])) {
		// Stores in Array
		$_SESSION = array();
		// Swipe via memory
		if (ini_get("session.use_cookies")) {
			// Prepare and swipe cookies
			$params = session_get_cookie_params();
			// clear cookies and sessions
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		// Just in case.. swipe these values too
		ini_set('session.gc_max_lifetime', 0);
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 1);
		// Completely destory our server sessions..
		session_destroy();
		die('1');
	}




?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
		<title>notiz.cc admin</title>
		
		<meta property="og:title" content="notiz.cc" />
		<meta property="og:site_name" content="notiz.cc" />
		<meta property="og:url" content="http://notiz.cc" />
		<meta property="og:image" content="favicon.ico" />
		<link rel="manifest" href="/admin/manifest.json">	
		<meta name="theme-color" content="#000000">
		
		<script src="../lib/js/jquery.min.js"></script>


		
		<!-- Raven -->
		<script src="https://cdn.ravenjs.com/3.8.0/raven.min.js"></script>
		<script>
			Raven.config('https://15da66d440c144ddb5a5b9ff6b2b7042@sentry.io/112192').install()
		</script>		

		<!-- Materialize.CSS -->
		<link rel="stylesheet" href="../lib/css/materialize.min.css">
		<script src="../lib/js/materialize.min.js"></script>

		<link rel="shortcut icon" href="../favicon.ico">
		
		<link rel="stylesheet" href="../lib/css/icon.css">
		<link rel="stylesheet" href="user.css">		
		

		<script type="text/javascript">
		
			function killAll() {
				$.post("admin.php", {
					killAll: Math.random()
					},
					function(data){
						if (data == 1) {
							Materialize.toast("Alle Sessions wurden zerstört", 8000);
						} else {
							Materialize.toast(data, 8000);
						}
					});
				
			}
			
			<?php require_once("user.js.php") ?>;
		</script>
		
	</head>

	<body>
		<!--header-bar-->	
		<nav>      
				<div class="nav-wrapper grey darken-4">
						<a href="" class="admin-logo brand-logo">notiz.cc admin</a>
				</div>
			</nav>
			<main>
	  
		<div id="button">	
			<div class="fixed-action-btn horizontal" style="bottom: 45px; right: 24px;">
				<a class="btn-floating btn-large red" onclick="killAll()" target=_blank>
					<i class="large material-icons">&#xE92B;</i>
				</a>
		</div>
		
		<main>
			<div class="liste">
				<?php Admin::html_list() ?>
			</div>
		</main>

		
		<footer class="page-footer grey darken-4">
			<div class="container">
				<div class="row">
					<div class="col l6 s12">
						<h4><a class="white-text" href="../">notiz.cc</a></h4>
						<h5 class="white-text">User: <?php echo $_SESSION['user']?></h5>
						<h5 class="white-text">DB: <?php echo $db->host_info?></h5>


						<p class="grey-text text-lighten-4"></p>
					</div>
					<div class="col l4 offset-l2 s12">
						<h5 class="white-text">Menü</h5>
						<ul>
							<li><a class="grey-text text-lighten-3" href="../lib/php/genpw.php" target=_blank>Passwörter generieren</a></li>
							<li><a class="grey-text text-lighten-3" href="db.php?server=jfrpocyduwfg38kq.chr7pe7iynqr.eu-west-1.rds.amazonaws.com" target=_blank>Datenbank Admin</a></li>
							<li><a class="grey-text text-lighten-3" href="https://dashboard.heroku.com/apps/timsnotiz" target=_blank>Heroku</a></li>
							<li><a class="grey-text text-lighten-3 pointer btn" onclick="logOut()">LogOut</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="footer-copyright black">
				<div class="container">
					<p>&copy; timlai_ 2016</p>
				</div>
			</div>
		</footer>
	<body>
</html>