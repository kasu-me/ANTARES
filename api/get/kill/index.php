<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD] && in_array($_SESSION["name"],$ADMIN_USER_NAMES)){
	$command="ps ax | grep \"".$SIMUTRANS_BIN."\" | grep -v grep";
	exec($command,$output);
	if(count($output)>0){
		$pid=trim(explode(" ",preg_replace('/\s+/',' ',$output[0]))[0]);
		exec("kill -9 ".$pid);
		header('HTTP/1.0 204');
	}else{
		header('HTTP/1.0 404');
		echo '{"message":"Simutransが起動していません。","type":"error"}';
	}
}else{
	header('HTTP/1.0 401');
	echo "{}";
}
?>