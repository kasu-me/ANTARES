<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD]){
	$nettool_command='export SIMUTRANS_DIR=\''.$SIMUTRANS_DIR.'\';nohup '.$SIMUTRANS_BIN.' -server '.$SIMUTRANS_SERVER_PORT.' -server_admin_pw "'.$SIMUTRANS_ADMIN_PASSWORD.'" -objects '.$SIMUTRANS_PAKSET.' -lang '.$SIMUTRANS_LANG.' > '.$SIMUTRANS_LOG_PATH.' &';
	exec($nettool_command,$nettool_output);
	echo "did";	
}else{
	header('HTTP/1.0 401');
	echo "{}";
}
?>