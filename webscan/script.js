function toggle_add() {

	if (menu == 0) {

		$.post('api.php', {show_add: 1}, function(data) {
			$('#add-dialog').html(data);
			$("#title").select()})

			menu = 1;

	} else {
		$('#add-dialog').html('');
		menu = 0
	}
}

function lister() {

	$(".progress").removeClass('hide');

	$.post('api.php', {lister: 1}, function(data) {

		$('#lister').html(data);
		$(".progress").addClass('hide');
		Materialize.toast("reloaded", 500);
	})
}

function add_test() {
	$.post("api.php", {add_test: 1, url: $("#url").val(), element: $("#element").val(), delimiter: $("#delimiter").val(), type: $("#type").is(":checked")}, function(data) {
		result = JSON.parse(data);
		if (result.success == 1) {
			$("#test_result").html(JSON.parse(data).msg)
		} else {
			Materialize.toast(result.msg);
		}
	})
}

function add() {
	$("#test_result").html('');
	$.post("api.php", {add: 1, title: $("#title").val(), url: $("#url").val(), element: $("#element").val(), delimiter: $("#delimiter").val(), type: $("#type").is(":checked")}, function(data) {
		
		result = JSON.parse(data);
		if (result.success == 1) {

			text = '<code><pre>' + result.msg + '</code></pre>';
			$("#title").val('');
			$("#url").val('');
			$("#element").val('');
			$("#delimiter").val('');
			$("#title").select();
			Materialize.updateTextFields();

		} else if (result.success == 'E3') {

			$("#title").addClass("invalid");
			$("#title").select();
			text = result.msg;

		} else {
			text = result.msg;	
		}
		Materialize.toast(text, 4000)
	})
}

function check() {

	$(".progress").removeClass('hide');


	$.post("api.php", {html_check: 1}, function(data) {

		$('#lister').html(data);
		$(".progress").addClass('hide');
		Materialize.toast("scaned", 500);

	})
}

function viewed(id) {
	$.post('api.php', {viewed: id}, function(data) {
		lister();
	})
}


$(function() {
	menu = 0;

	window.setInterval(function(){
		if (document.getElementById("checkbox_reload").checked) {
			lister()
		}
	}, 60000)

})

