<!-- Underscore.js 1.8.3-->
<script src="lib/js/underscore.min.js"></script>

<script>

	var keypressed = 0;

	//wenn die Seite geladen ist
	
	$(function() {
		console.log("js is ready");

		$("#content").tabby();
		$("#content").focus();
		<?php 
		if ($note_password == '') {
			echo "$('#pwfieldarea').removeClass('hide')";
		}
		?> 		
		//Passwort setzen
		$('#addpasswordform').submit(function() {
			event.preventDefault();
			$.post("api.php", {
				note_name: "<?php echo $note_name?>",
				note_password_set: $('#addpassword').val(),
			},
			function(data){
				if (data == '1') {
					window.location = note_name + '#pw';
					location.reload();
				} else if (data == '2') {
					window.location = note_name + '#pwset';
					location.reload();
				} else {
					Materialize.toast(data, 4000);
				}

			});

			$('#addpassword').val('');
		})
	})
	
	//Speichert den Text
	function saveNote() {
		keypressed = 1;
		$.post("api.php", {
			note_newcontent: rfc3986EncodeURIComponent(jQuery("textarea[name='note_content']").val()),
			note_name: "<?php echo $note_name?>",
			change: change,
		},
		function(data, status){
			if (date == "Notiz mit Passwort gelöscht") {
				$('#pwfieldarea').removeClass("hide");
			} else if (data == "Kein Zugriff! Seite bitte neuladen") {
				location.reload();
			}
			Materialize.toast(data, 4000);
		});
	}
	
	//Keyboardshortcuts
	$(window).bind('keydown', function(event) {
		if (event.ctrlKey) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
				case 's':
				event.preventDefault();
				saveNote();
				Materialize.toast('gespeichert.', 1000)
				break;
			}
		}
	});	

	
	//wenn man TAB drückt soll auch gespeichert werden
	$(document).keyup(function(e) {
		if (e.keyCode == 9) saveNote();
		
	});

	//wenn man eine tastedrückt
	$("#textarea").keyup(function() {
		keypressed = 1;		
	});

//Roher Text

	//apostrophe fix
	function rfc3986EncodeURIComponent(str) {  
		return encodeURIComponent(str).replace(/[!'()*]/g, escape);  
	}
	
	//Textarea updaten
	function refresh_raw(text) {
		if (decodeURIComponent(text) != _.unescape($('#textarea').val()) && keypressed == 0) {
			$('#textarea').val(decodeURIComponent(decodeURIComponent(text)));
		}
	}

	//Verzögerungs Funktion
	function debounce(fn, delay) {
		var timer = null;
		return function () {
			var context = this, args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () {
				fn.apply(context, args);
			}, delay);
		};
	}
	
	//x Sekunden nach Tastedruck wird die Variable wieder Null gesetzt
	$(document).keypress(debounce(function (event) {
		keypressed = 0;
	},  5000));

	window.setInterval(function() {
		if (keypressed == 0) {
			refresh_raw(text)
		}
	}, 1000);
	

</script>