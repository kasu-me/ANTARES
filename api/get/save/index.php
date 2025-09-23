<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

onlyAllowAuthenticated();

//プロセスが起動していない場合は何もしない
if(!isSimutransRunning()){
	header('HTTP/1.0 404');
	echo '{"message":"Simutransが起動していません。","type":"error"}';
	exit();
}

//プロセスが起動している場合は保存する
$nettool_command='export SIMUTRANS_DIR=\''.$SIMUTRANS_DIR.'\';'.$SIMUTRANS_NETTOOL.' -p "'.$SIMUTRANS_ADMIN_PASSWORD.'" say "20秒後にセーブを行います。";sleep 20;'.$SIMUTRANS_NETTOOL.' -p "'.$SIMUTRANS_ADMIN_PASSWORD.'" force-sync;wait;';
exec($nettool_command,$nettool_output);
header('HTTP/1.0 204');
?>