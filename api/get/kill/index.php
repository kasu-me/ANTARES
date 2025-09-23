<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

onlyAllowAdmin();

if(isSimutransRunning()){
	$process_check_command='ps aux | grep "'.$SIMUTRANS_BIN.' -server" | grep -v grep';
	exec($process_check_command,$process_check_output);	
	$pid=trim(explode(" ",preg_replace('/\s+/',' ',$process_check_output[0]))[1]);
	exec("kill -9 ".$pid);
	header('HTTP/1.0 204');
}else{
	header('HTTP/1.0 404');
	echo '{"message":"Simutransが起動していません。","type":"error"}';
}
?>