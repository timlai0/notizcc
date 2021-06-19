<?php

@session_start();
if (empty($_SESSION['admin']) OR $_SESSION['admin'] == 0) {
	header('Location: ./');
	die();
} 

?>

<!DOCTYPE html>
<html>
<head>
	<title>WebScan</title>
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

	.pointer {
		cursor: pointer;
	}

	#lister tr, #lister td {
		border: 1px solid black;
		background-color: #E6E6E6;

	}

	#lister table,   {
		border: 1px solid black
	}

	#lister {
		padding: 0;
	}

	#lister {
		margin-top:  2em;
	}

	.title a {
		height: 100%;
	}

	pre .box {
		white-space: pre-wrap;       /* css-3 */
		white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
		white-space: -pre-wrap;      /* Opera 4-6 */
		white-space: -o-pre-wrap;    /* Opera 7 */
		word-wrap: break-word;       /* Internet Explorer 5.5+ */
	}

	.no_margin {
		padding: 0;
		margin: 0;
	}

	.date {
		width: 4em;
		text-align: center;
	}

	.title {
		width: 16em;
	}

	.progress {
		position: relative;
		bottom: 0;
		margin: 0;
	}

</style>

</head>
<body>

	<nav class="grey row">
		<div class="col s0 m1"></div>
		<div class="col s12 m10 nav-wrapper">
			<a href="" class="brand-logo">WebScan</a>
			<ul id="nav-mobile" class="right">

				<li><div id="last_change"></div></li>
				<li>
					<div class="switch tooltipped hide-on-med-and-down" data-position="bottom" data-delay="50" data-tooltip="Auto-Reload">
						<label class="white-text">
							<input type="checkbox" id="checkbox_reload">
							<span class="lever"></span>
						</label>
					</div>

				</li>


				<li><a onclick="check()" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Check"><i class="material-icons">autorenew</i></a></li>

				<li><a onclick="toggle_add()" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Neu"><i class="material-icons">add_box</i></a></li>
			</ul>
		</div>


		<div class="col s0 m1"></div>
		
		<div class="hide progress scale-transition">
			<div class="indeterminate"></div>
		</div>

	</nav>

	<div class="row">
		<div class="col s0 m1"></div>

		<div class="col s12 m10">

			<div id="add-dialog">
			</div>

			<div id="lister">

				<?php require_once 'php.php'; echo Webscan_HTML::lister();?>
			</div>
		</div>
		<div class="col s0 m1"></div>

	</div>

</body>
</html>