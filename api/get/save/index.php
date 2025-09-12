<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD]){
	$nettool_command='export SIMUTRANS_DIR=\''.$SIMUTRANS_DIR.'\';'.$SIMUTRANS_NETTOOL.' -p "'.$SIMUTRANS_ADMIN_PASSWORD.'" say "20秒後にセーブを行います。";sleep 20;'.$SIMUTRANS_NETTOOL.' -p "'.$SIMUTRANS_ADMIN_PASSWORD.'" force-sync;wait;';
	exec($nettool_command,$nettool_output);
	header('HTTP/1.0 204');
}else{
	header('HTTP/1.0 401');
	echo "{}";
}
?>