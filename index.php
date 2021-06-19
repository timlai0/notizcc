<?php

include_once "config.php";
$jsbuffer = "";

//Verlauf
if (!isset($_SESSION['history'])) {
	$_SESSION['history'] = array();
}


//userid
if (isset($_SESSION['userId'])) {
	$user_id = $_SESSION['userId'];
} else {
	$user_id = 0;
}

//Note Name verarbeiten:
if (isset($_GET["f"])) {
	$note_name = $_GET["f"];

	$getNoteInfo = $db->query("SELECT * FROM  `notes` WHERE  `notes_name` = '$note_name' LIMIT 0 , 1");
	$row = mysqli_fetch_row($getNoteInfo);

	//wenn es den Namen gibt, alle Daten auslesen und aus dem Array holen
	if (!is_null($row)) {

		$note_id = $row[0];
		$note_name = $row[1];
		$note_content = rawurldecode(rawurldecode($row[2]));
		$note_creator = $row[3];
		$note_lastchange = $row[4];
		$note_lastchanger = $row[5];
		$note_password = $row[6];
		$note_adminonly = $row[7];
		$note_change = $row[8];

		if (strpos($note_content, "<script>") !== false) {
			$jsbuffer .= '<script>if (window.confirm("Achtung! Jemand hat zu dieser Notiz JavaScript hinzugefügt welches Dein Gerät schädigen kann.")) {} else {window.location.href = "."};</script>';
		}
	} else {

		//neuer Note
		$note_content = "";
		$note_creator = $user_id;
		$note_lastchange = "nie";
		$note_lastchanger = $user_id;
		$note_password = "";
		$note_adminonly = 0;
		$note_change = 0;
	}
} else {
	//wenn kein Name angegeben ist
	$lines = file(dirname(__FILE__) . "/words.txt");
	$note_name = $lines[rand(1, count($lines))];
	header('Location:' . $note_name);
	die();
}

//list exist
if (isset($_SESSION['history'][0])) {

	//already in list
	if (in_array($note_name, $_SESSION['history'])) {

		unset($_SESSION['history'][array_search($note_name, $_SESSION['history'])]);
		$_SESSION['history'] = array_values($_SESSION['history']);
		array_unshift($_SESSION['history'], $note_name);
	}

	//not set as latest
	elseif ($_SESSION['history'][0] != $note_name) {

		array_unshift($_SESSION['history'], $note_name);
	}
} else {
	$_SESSION['history'][0] = $note_name;
}

//shorten the list
$_SESSION['history'] = array_slice($_SESSION['history'], 0, 4);

if (($user_id == $note_creator and $user_id != 0) or (!empty($_SESSION['access']) and in_array($note_name, $_SESSION['access'])) or empty($note_password) or !empty($_SESSION['admin'])) {
	$access = 1;
} else {
	$access = 0;
}

?>

<!DOCTYPE html>

<head>
	<!-- Meta Stuff -->
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>notiz.cc | <?php echo $note_name ?></title>

	<!-- Meta Stuff -->
	<meta property="og:title" content="notiz.cc | <?php echo $note_name ?>" />
	<meta property="og:description" content="notiz.cc" />
	<meta property="og:site_name" content="notiz.cc | <?php echo $note_name ?>" />
	<meta property="og:url" content="http://notiz.cc" />
	<meta property="og:image" content="http://notiz.cc/lib/img/512.png" />
	<meta name="keywords" content="notes,notiz.cc,notiz,url">
	<link rel="manifest" href="/manifest.json">
	<meta name="theme-color" content="#000">

	<link rel="shortcut icon" href="favicon.ico">

	<!-- JS -->
	<!-- jquery -->
	<script src="lib/js/jquery.min.js"></script>
	<script src="lib/js/jquery.textarea.js"></script>

	<!-- Materialize.CSS -->
	<link rel="stylesheet" href="lib/css/materialize.min.css">
	<script src="lib/js/materialize.min.js"></script>

	<!-- highlight.js -->
	<link rel="stylesheet" href="lib/css/highlight.min.css">
	<script src="lib/js/highlight.min.js"></script>
	<script>
		hljs.initHighlightingOnLoad();
	</script>

	<!-- Clipboard.js -->
	<script src="lib/js/clipboard.min.js"></script>

	<!-- JavaScriptBasis -->
	<?php include_once "js_basic.php" ?>

	<!-- JavaScript notiz.cc -->
	<?php if ($access == 1) {
		include_once "js.php";
		echo $jsbuffer;
	} ?>

	<!-- CSS -->
	<link rel="stylesheet" href="css.css">

</head>

<body class="notes">
	<!--header-bar-->
	<div class="navbar-fixed">
		<nav>
			<div class="nav-wrapper black">
				<div <?php if (!empty($_SESSION['user'])) : ?> class="green" <?php endif ?>>

					<a onclick="menu()" class="center pointer" style="float:left; width: 64px; height: 64px;"><img src="lib/img/icon/menu.svg" /></a>
				</div>
				<div class="input-field" style="margin-left: 64px">
					<input id="search" type="search" name="gotonote" required pattern="^[a-zA-Z0-9\-]+$">
					<label class="label-icon" for="search"><i class="material-icons" id="searchlabel">&#xE8B6;</i></label>
					<i class="material-icons">close</i>
				</div>
			</div>
		</nav>
	</div>

	<!--slide-out menu-->
	<ul id="slide-out" class="side-nav" style="padding-top: 64px">
		<span class="cursors_none">
			<li class="center">
				<h3>notiz.cc</h3>
			</li>
		</span>
		<hr />
		<li><a class="pointer" id="mdbutton" onclick="changeMDandHideNav()">Markdown</a></li>
		<li><a href="md/<?php echo $note_name ?>">Markdown HTML</a></li>
		<li><a href="html/<?php echo $note_name ?>">HTML</a></li>
		<li><a href="txt/<?php echo $note_name ?>">TXT</a></li>

		<div id="pwfieldarea" class="hide">

			<hr />
			<div class="input-field pwfield">
				<form id="addpasswordform" method="POST">
					<input name="note_password_set" id="addpassword" type="password" class="validate">

					<label for="password">Passwort setzen</label>
				</form>
			</div>
		</div>

		<hr />
		<p class="timestamp center">Letztes Update <span id="date"><?php echo $note_lastchange ?></span><br /> mit der Nummer <span id="changeNr"><?php echo $note_change ?></span></p>


		<hr />

		<ul class="collapsible" data-collapsible="accordion">
			<li>
				<div class="collapsible-header"><i class="material-icons">history</i>Verlauf</div>
				<div class="collapsible-body">
					<ul>
						<?php
						foreach ($_SESSION['history'] as $p) {
							echo '<li><a href="' . $p . '">' . $p . '</a></li>';
						}
						?>
					</ul>
				</div>
			</li>
			<li>
				<div class="collapsible-header"><i class="material-icons">&#xE312;</i>Tastenkombos</div>
				<div class="collapsible-body">
					<span>STRG + Y -> Markdown<br />STRG + S -> speichern</span>
				</div>
			</li>
			<li>
				<div class="collapsible-header"><i class="material-icons">info</i>Infos</div>
				<div class="collapsible-body">
					<span>hmm.</span>
				</div>
			</li>
		</ul>
		<li>
			<div class="collapsible-header" onclick="window.open('https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet', '_blank')">
				<i class="material-icons">chrome_reader_mode</i>Markdown Hilfe
			</div>
		</li>
		<li>
			<div class="collapsible-header" onclick="window.open('https://notiz.cc/sprit/', '_blank')">
				<i class="material-icons">local_gas_station</i>Lokaler Benzinpreis
			</div>
		</li>
		<hr />

		<li>
			<div class="collapsible-header" onclick="window.open('https://www.sharepa.com/', '_blank')">
				<i class="material-icons">cloud_upload</i>Uploadservice A
			</div>
		</li>

		<li>
			<div class="collapsible-header" onclick="window.open('https://workupload.com/', '_blank')">
				<i class="material-icons">cloud_upload</i>Uploadservice B
			</div>
		</li>

		<hr />

		<li>
			<div class="collapsible-header" onclick="window.open('http://notiz.cc/pgp/', '_blank')">
				<i class="material-icons">lock_open</i>PGP Tool
			</div>
		</li>

		<hr />

		<li>
			<?php
			if (!empty($_SESSION['user'])) {

				$user = $_SESSION['user'];
				echo "<a href='user'>Username: $user</a>";
				echo "<li><a onclick='logOut()' class='pointer'>Ausloggen</a></li>";
			} else {
				echo '<a href="login">Login</a>';
			}
			?>
		</li>

		<li><a class="pointer" onclick="delSession()">Session zerstören</a></li>

		<hr />
		<li><a href="timlai#md" target="_blank">@timlai</a></li>

		<hr />

	</ul>
	<a data-activates="slide-out" class="button-collapse"><i class="mdi-navigation-menu"></i></a>

	<!--________________________________________________-->
	<div id="content">
		<?php
		if ($access == 1) {
			echo '<textarea spellcheck="false" id="textarea" name="note_content"';
			if ($note_adminonly != 1) {
				echo 'oninput="saveNote()"';
			} else {
				echo 'readonly ';
			}
			echo 'autofocus>';
			if (isset($note_content)) {
				echo $note_content;
				echo '</textarea>';
			}
		} else {
			echo '
		<div class="valign-wrapper">
		<div class="row">
		<div class="col s12">
		<div class="card blue-grey darken-2">
		<a onclick="dispMDsilent();" class="pointer">
		<div class="card-content white-text">
		<h4 class="center-align truncate">' . $note_name . '</h4>
		</div>
		</a>
		<div class="card-action pwfield">
		<div class="input-field">
		<form action="api.php" method="POST">
		<input name="note_password" id="password" type="password" class="validate" autofocus>
		<label for="password">Passwort</label>
		<button type="submit" name="note_name_pw" value="' . $note_name . '" class="waves-effect waves-light btn black fw" autofocus>
		Senden
		<i class="material-icons right">lock_open</i></button>
		</form>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>';
		}
		?>
	</div>
	<div id="markdown" class="z-depth-2">
		<div id="readcode" class="white"><?php echo Html::text_markdown($note_content); ?></div>
	</div>
</body>

</html>