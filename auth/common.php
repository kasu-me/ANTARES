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

function isLoginAndMember(){
	global $SESSION_ID_DETERMINE_GUILD;
	return isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD];
}

function isAdmin(){
	global $ADMIN_USER_NAMES;
	return isLoginAndMember() && in_array($_SESSION["name"],$ADMIN_USER_NAMES);
}

function onlyAllowAuthenticated(){
	if(!isLoginAndMember()){
		header('HTTP/1.0 401');
		echo '{"message":"ログインしていません。","type":"error"}';
		exit();
	}
}
function onlyAllowAdmin(){
	if(!isAdmin()){
		header('HTTP/1.0 401');
		echo '{"message":"管理者権限がありません。","type":"error"}';
		exit();
	}
}
?>