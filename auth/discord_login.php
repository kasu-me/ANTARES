<?php
include("discord.php");

//ログイン状態
if(isLogIn()) {
	//ユーザー情報取得
	$user = apiRequest($apiURLBase);

	//情報をDBおよびセッションに登録する
	$users = sendSQL("INSERT INTO login_users (id,name) VALUES ($1,$2)",array(session('access_token'),$user->username));
	$_SESSION['name'] = $user->username;

	if(isJoinedAllowedGuild()){
		$_SESSION[$SESSION_ID_DETERMINE_GUILD]=true;
	}else{
		$_SESSION[$SESSION_ID_DETERMINE_GUILD]=false;
	}	
} else {
	//ログインしていない状態
}
header('Location: ' . "https://".$_SERVER["SERVER_NAME"]."/");

?>