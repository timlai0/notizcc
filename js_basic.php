<script>

	if (location.protocol != 'https:') {
		location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
	}

	var note_name = "<?php echo $note_name?>";
	var change = "<?php echo $note_change?>";
	var text = <?php echo json_encode($note_content)?>;

	$(function() {
		console.log("js_basic is ready");
		
		//menu init
		$(".button-collapse").sideNav();

		//auto refresh Markdown
		window.setInterval(function(){
			refresh(); }, 1000);
		
		//Markdown anzeigen
		if (location.hash=="#md") {
			dispMD();
		}

		//bei falschem Passwort
		if (location.hash=="#wpw") {
			Materialize.toast('Falsches Passwort!', 3000);
			history.pushState('', document.title, window.location.pathname);
		}
		
		//Passwort gesetzt
		if (location.hash == "#pw") {
			Materialize.toast('Passwort wurde erfolgreich gesetzt', 3000);
			history.pushState('', document.title, window.location.pathname);
		}
		
		//Passwort schon gesetzt
		if (location.hash == "#pwset") {
			Materialize.toast('Passwort wurde schon gesetzt', 3000);
			history.pushState('', document.title, window.location.pathname);
		}
		
		//gerade ausgelogt
		if (location.hash=="#lo") {
			Materialize.toast('Erfolgreich Ausgelogt', 3000);
			history.pushState('', document.title, window.location.pathname);
		}

		<?php 
		if ($access == 0) {
			echo 'dispMDsilent()';
		}
		?>	

		//nur Alphanumerical für das Suchfeld erlauben aber nicht das Submitting beeinflussen
		$('#search').keypress(function (e) {
			if(!(e && e.keyCode == 13)) {
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

		//Clipboard Funktionen
		var urlclipboard = new Clipboard('.urlclip');
		var clipboard = new Clipboard('.clip');

		urlclipboard.on('success', function(e) {
			Materialize.toast('URL kopiert!', 2000)
		});
		
		clipboard.on('success', function(e) {
			Materialize.toast('Text kopiert!', 2000)
		});

	})

	//refresh Text Parsed Text
	function refresh() {
		$.post("api.php", {
			note_name: note_name,
			note_refresh: ""
		},
		function(data){
			var ans = JSON.parse(data);
			$('#changeNr').html(ans[0]);
			$('#date').html(ans[1]);

			text = ans[2];

			if (change != ans[0]) {
				change = ans[0];
				
				$('#readcode').html(ans[3]);

				$('#readcode pre code').each(function(i, block) { 
					hljs.highlightBlock(block); 
				});
			}
		});
	}
	
	//Keyboardshortcuts
	$(window).bind('keydown', function(event) {
		if (event.ctrlKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
				case 'y':
				event.preventDefault();
				changeMD();
				break;
			}
		}
	});	
	
	//session löschen
	function delSession() {
		$.post("api.php", {
			delSession: Math.random()
		},
		function(data){
			Materialize.toast(data, 3000);
			location.reload();
		});
	}
	
	//ausloggen
	function logOut() {
		$.post("api.php", {
			logOut: Math.random()
		},
		function(data){
			if (data == 1) {
				window.location.replace("<?php echo $_SESSION['history'][0]?>#lo");
				location.reload();
			}
		});
		
	}


	//CSS Änderung-----------------------------------------------------

	//öffnet oder schließt dass Menu
	function menu() {
		$('.button-collapse').sideNav('show')
	}
	
	function changeMDandHideNav() {  
		$('.button-collapse').sideNav('hide');
		changeMD();
	}
	
	function changeMD() {
		if ($('#markdown').css('display') == 'block') {
			goBack();
		}
		else {
			dispMD();
		}
	}
	
	function dispMD() {
		location.hash="md";
		dispMDsilent();
	}
	
	function dispMDsilent() {
		$("#markdown").css({display: "block"});
		$("#content").css({display: "none"});	
		$("#mdbutton").html('zum Editor');
	}
	
	function goBack() {
		history.pushState('', document.title, window.location.pathname);
		$("#markdown").css({display: "none"});
		$("#content").css({display: "block"});
		$("#mdbutton").html('Markdown');
	}

</script>