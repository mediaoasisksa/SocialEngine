<?php
// JWT details of Mayankg17@gmail.com Zoom's account
$api_key = 'ErwLSzf_R5yMeXwBWwAjfg';
$api_secret = 'DnGJdJqKbu1Xfb4bJsZwCK7sJwpP4XKI';
$meeting_number = '81398709197';
$role = '0'; //0 for attendee or 1 for host
function generate_signature ( $api_key, $api_secret, $meeting_number, $role){

	$time = time() * 1000 - 30000;//time in milliseconds (or close enough)
	
	$data = base64_encode($api_key . $meeting_number . $time . $role);
	
	$hash = hash_hmac('sha256', $data, $api_secret, true);
	
	$_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
	
	//return signature, url safe base64 encoded
	return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
}

echo generate_signature ( $api_key, $api_secret, $meeting_number, $role);
?>