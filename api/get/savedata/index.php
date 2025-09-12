<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD]){
	echo file_get_contents($SIMUTRANS_SAVEDATA_PATH);
}else{
	header('HTTP/1.0 401');
	echo "{}";
}
?>