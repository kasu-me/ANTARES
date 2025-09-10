<?php
function get($key, $default=NULL) {
return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

function isLogIn(){
	return session('access_token');
}
?>