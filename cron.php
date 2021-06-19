<?php

ob_end_clean();
header("Connection: close");
ignore_user_abort(true); // just to be safe
ob_start();
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush(); // Strange behaviour, will not work
flush(); // Unless both are called !
// Do processing here 




$ar_target = array(
	'https://notiz.cf/webscan/api.php'
);




foreach ($ar_target as $target) {
	$ch = curl_init($target);
	curl_exec($ch);
	curl_close($ch);
}
?>