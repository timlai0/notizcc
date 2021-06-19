<?php
if (!empty($_SESSION['history'][0])) {
	$fo = $_SESSION['history'][0];
} else {
	$fo = 'timlai';
}
?>

function deleteNote(note_name) {
	console.log("test");
	$.post("../api.php", {
		deleteNote: 1,
		note_name: note_name
		},
		function(data){
			if (data == 0) {
				Materialize.toast("FEHLER: " + note_name + " wurde nicht gelöscht", 4000);
				console.log(data);			
			} else {				
				Materialize.toast(note_name + " gelöscht", 4000);
				$('.liste').html(data);
			}
		});
}

function logOut() {
	$.post("../api.php", {
		logOut: ""
		},
		function(data){
			if (data == 1) {
				window.location.replace("../<?php echo $_SESSION['history'][0]?>#lo");
			} else {
				console.log(data);
			}
		});	
}

	$(function() {
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
		window.location.href = "../" + $('#search').val();
	}
});
})