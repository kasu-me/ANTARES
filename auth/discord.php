<?php
include("common.php");
include("db.php");
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); 

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', $DISCORD_OAUTH2_CLIENT_ID);
define('OAUTH2_CLIENT_SECRET', $DISCORD_OAUTH2_CLIENT_SECRET);

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$apiURLGuild = 'https://discord.com/api/users/@me/guilds';
$revokeURL = 'https://discord.com/api/oauth2/token/revoke';

session_start();
session_regenerate_id();

// ユーザーをDiscordの認証ページに転送してログインプロセスを開始
if(get('action') == 'login') {
	$params = array(
		'client_id' => OAUTH2_CLIENT_ID,
		'redirect_uri' => 'https://'.$_SERVER["SERVER_NAME"].'/auth/discord_login.php',
		'response_type' => 'code',
		'scope' => 'identify guilds'
	);

	// ユーザーをDiscordの認証ページにリダイレクトする
	header('Location: ' . $authorizeURL . '?' . http_build_query($params));
	die();
}

// Discordがユーザーをここにリダイレクトすると、クエリ文字列に「code」と「state」パラメータが含まれる
if(get('code')) {
	// 認証コードをトークンに交換する
	$token = apiRequest($tokenURL, array(
		"grant_type" => "authorization_code",
		'client_id' => OAUTH2_CLIENT_ID,
		'client_secret' => OAUTH2_CLIENT_SECRET,
		'redirect_uri' => 'https://'.$_SERVER["SERVER_NAME"].'/auth/discord_login.php',
		'code' => get('code')
	));
	$logout_token = $token->access_token;
	$_SESSION['access_token'] = $token->access_token;

	header('Location: ' . $_SERVER['PHP_SELF']);
}

// ログアウト処理
if(get('action') == 'logout') {
	logout($revokeURL, array(
		'token' => session('access_token'),
		'token_type_hint' => 'access_token',
		'client_id' => OAUTH2_CLIENT_ID,
		'client_secret' => OAUTH2_CLIENT_SECRET,
	));
	unset($_SESSION['access_token']);
	unset($_SESSION[$SESSION_ID_DETERMINE_GUILD]);
	unset($_SESSION["name"]);
	header('Location: ' . $_SERVER['PHP_SELF']);
	die();
}

//APIリクエスト
function apiRequest($url, $post=FALSE, $headers=array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$response = curl_exec($ch);

	if($post)
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

	$headers[] = 'Accept: application/json';

	if(session('access_token'))
		$headers[] = 'Authorization: Bearer ' . session('access_token');

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$response = curl_exec($ch);
	return json_decode($response);
}

//ログアウト
function logout($url, $data=array()) {
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
		CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
		CURLOPT_POSTFIELDS => http_build_query($data),
	));
	$response = curl_exec($ch);
	$users = sendSQL("DELETE FROM login_users WHERE id=$1",array(session('access_token')));
	return json_decode($response);
}

//サーバに参加しているかを判定
function isJoinedAllowedGuild(){
	global $apiURLGuild;
	global $DISCORD_GUILD_ID;
	$guilds = apiRequest($apiURLGuild);
	
	foreach ($guilds as $guild) {
		if ($guild->id==$DISCORD_GUILD_ID) {
			return true;
		}
	}
	return false;
}
?>