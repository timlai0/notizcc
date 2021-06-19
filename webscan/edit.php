<?php

require_once "php.php";



   // $cookie = tempnam ("/cache_session", "1561606872092");
 //   $cookie = tempnam ("/Seminar_Session", "e7a7e9cf4dab941123adee2a17e2a3d7");
//'Seminar_Session=e7a7e9cf4dab941123adee2a17e2a3d7'

function get_web_page( $url, $cookiesIn = '', $post = ''){
	$options = array(
		CURLOPT_RETURNTRANSFER => true,     // return web page
		CURLOPT_HEADER         => true,     //return headers in addition to content
		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		CURLOPT_ENCODING       => "",       // handle all encodings
		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
		CURLOPT_TIMEOUT        => 120,      // timeout on response
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		CURLINFO_HEADER_OUT    => true,
		CURLOPT_SSL_VERIFYPEER => true,     // Validate SSL Certificates
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_COOKIE         => $cookiesIn,
		CURLOPT_COOKIEJAR      => 'cookie.txt',
		CURLOPT_COOKIEFILE     => 'cookie.txt'
	);

	if (!empty($post)) {
		$options[CURLOPT_POST] = true;
		$options[CURLOPT_POSTFIELDS] = $post;
	}

	$ch      = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$rough_content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	curl_close( $ch );

	$header_content = substr($rough_content, 0, $header['header_size']);
	$body_content = trim(str_replace($header_content, '', $rough_content));
	$pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m"; 
	preg_match_all($pattern, $header_content, $matches); 
	$cookiesOut = implode("; ", $matches['cookie']);

	$header['errno']   = $err;
	$header['errmsg']  = $errmsg;
	$header['headers']  = $header_content;
	$header['content'] = $body_content;
	$header['cookies'] = $cookiesOut;
	return $header;
}


$a1 = get_web_page("https://studip.leibniz-fh.de/index.php?again=yes", "", "loginname=fh1841308&password=wcn0tj&security_token=3Cu7LzUuj%2F4HrsGImNeOINanbEybwm%2F9Npr34E2o%2BFOwvv%2Bc165%2B&login_ticket=6d078c0dd5cfbb9762676b15acdee4de&resolution=2560x1440&device_pixel_ratio=1&Login=");

echo $a1['cookies'];
echo print_r(get_web_page('https://studip.leibniz-fh.de/plugins.php/markviewplugin/marks/show_test_results?cid=6d758c062a0f305df39c0b82a8917da9', $a1['cookies']));


?>