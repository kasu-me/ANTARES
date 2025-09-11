<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD]){
	//プロセスが起動している場合は何もしない
	$process_check_command='ps aux | grep "'.$SIMUTRANS_BIN.' -server" | grep -v grep';
	exec($process_check_command,$process_check_output);	
	if(count($process_check_output)>0){
		header('HTTP/1.0 409');
		echo '{"message":"Simutransはすでに起動しています。"}';
		exit();
	}

	//プロセスが起動していない場合は起動する
	$nettool_command='export SIMUTRANS_DIR=\''.$SIMUTRANS_DIR.'\';nohup '.$SIMUTRANS_BIN.' -server '.$SIMUTRANS_SERVER_PORT.' -server_admin_pw "'.$SIMUTRANS_ADMIN_PASSWORD.'" -objects '.$SIMUTRANS_PAKSET.' -lang '.$SIMUTRANS_LANG.' > '.$SIMUTRANS_LOG_PATH.' &';
	exec($nettool_command,$nettool_output);
	echo "did";	
}else{
	header('HTTP/1.0 401');
	echo "{}";
}
?>