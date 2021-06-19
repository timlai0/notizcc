<?php require_once 'chat.php'; ?>

<!DOCTYPE html>
<html>

<head>
	<title>notiz.cc | Chat</title>

	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>

	<script type="text/javascript" src="https://cdn.rawgit.com/alanhogan/Tabby/master/jquery.textarea.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">

	<script type="text/javascript">
		if (location.protocol != 'https:') {
			location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
		}

		function send() {

			if ($("#msg").val()) {

				lock = 1;

				$.post("api.php", {
					user: $("#user").val(),
					color: $("#color").val(),
					message: $("#msg").val()
				}, function(data) {
					append(data);


					$("#msg").val("");
					$('#msg').trigger('autoresize');

					window.scrollTo(0, document.body.scrollHeight);
				});

			} else {
				Materialize.toast("Leerer Text", 2000);
			}

			$("#msg").focus();

		}

		function view() {
			$.post("api.php", {
				view: 1
			}, function(data) {
				append(data);
			});
		}

		function append(data) {

			result = JSON.parse(data)

			if (last != result['lastmsg'].id) {

				$("#chat").html(result['html']);

				if (last != 0 & !(lock) & !(0)) {

					Materialize.toast(result['lastmsg'].user + ": " + result['lastmsg'].msg, 2000)
				}

				last = result['lastmsg'].id;
				lock = 0;
			}
		}


		function down() {
			window.scrollTo(0, document.body.scrollHeight);
			$("#msg").focus();
		}

		function isScrolledIntoView(el) {
			var elemTop = el.getBoundingClientRect().top;
			var elemBottom = el.getBoundingClientRect().bottom;

			var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
			return isVisible;
		}


		$(function() {

			lock = 0;
			last = 0;

			$('#msg').keydown(function(event) {
				if (event.keyCode == 13) {
					var content = this.value;
					if (event.shiftKey) {

						send();
					}
				}
			});

			setInterval(view, 500);

			view();

			$('#msg').tabby();


			$('#user').keypress(function(e) {
				if (!(e && e.keyCode == 13)) {
					var regex = new RegExp("^[a-zA-Z0-9\-]+$");
					var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
					if (regex.test(str)) {
						return true;
					}
					e.preventDefault();
					Materialize.toast('Nicht erlaubtes Zeichen', 1000);
					return false;
				} else {
					window.location.href = $('#search').val();
				}
			});
		});
	</script>


	<style type="text/css">
		body {
			display: flex;
			min-height: 100vh;
			flex-direction: column;
		}

		main {
			flex: 1 0 auto;
			margin-bottom: 200px;
		}

		main div.row {
			background-color: #ffffff;
		}

		footer {
			position: fixed;
			bottom: 0;
			width: 100%;
		}

		.msg {
			word-wrap: break-word;
		}

		.msg * {
			margin: 0;
		}

		.btn {
			width: 100%;
		}

		#chat div.row {
			border-bottom: 1px solid black;
			margin: 0;
		}

		input#color {
			border: 1px solid #9e9e9e;
			box-shadow: none;
		}

		.pointer {
			cursor: pointer;
		}

		.user,
		.time,
		.btnSend {
			font-weight: bold;
		}

		.btnSend {
			width: 100%;
		}

		textarea.materialize-textarea {
			padding: initial;
		}

		div#chatInput.row {
			margin: 0;
		}

		footer.page-footer {
			padding: 0;
		}

		div.input-field {
			margin-top: 4px
		}
	</style>



</head>

<body>

	<div class="navbar-fixed pointer hoverable" onclick="down()">
		<nav>
			<div class="nav-wrapper black row">

				<div class="col s10">
					<a href="#" class="brand-logo">Chat</a>

				</div>

				<?php Chat::loginBtn() ?>

			</div>
		</nav>
	</div>

	<main>

		<!-- wo der Chat hin soll -->
		<div id="chat" class="row"><?php Chat::display() ?></div>

	</main>

	<footer class="page-footer black">

		<?php if (!empty($_SESSION['user'])) : ?>

			<div id="chatInput" class="row">

				<div class="input-field col s12 m1">
					<input id="color" class="jscolor center" value="000000">
				</div>

				<div class="input-field col s12 m8 l10 center">
					<textarea id="msg" class="white-text materialize-textarea" autofocus></textarea>
				</div>

			<?php else : ?>

				<div id="chatInput" class="row">

					<div class="input-field col s10 m3 l1">
						<input id="user" class="white-text center" value="namenlos" maxlength="18">
					</div>

					<div class="input-field col s2 m1">
						<input id="color" class="jscolor center" value="000000">
					</div>

					<div class="input-field col s12 m8 l10 center">
						<textarea id="msg" class="white-text materialize-textarea" autofocus></textarea>
					</div>
				</div>

			<?php endif ?>

			<div class="footer-copyright  grey darken-4 center-align pointer" onclick="send()">
				<div class="center-align btnSend">Senden</div>
			</div>

			</div>

	</footer>

</body>

</html>