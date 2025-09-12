<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD] && in_array($_SESSION["name"],$ADMIN_USER_NAMES)){
	$queues=explode("\n",trim(file_get_contents($TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH)));
	
	$result=[];
	$result["list"]=[];
	foreach ($queues as $key => $value) {
		$addedPakInfo=explode(",",$value);
		if(count($addedPakInfo)!=3){
			continue;
		}
		$resultElement=[];
		$resultElement["author"]=$addedPakInfo[0];
		$resultElement["fileName"]=$addedPakInfo[1];
		$resultElement["description"]=$addedPakInfo[2];
		array_push($result["list"],$resultElement);
	}
	
	$result_json=json_encode($result, JSON_UNESCAPED_UNICODE);

	echo $result_json;
}else{
	header('HTTP/1.0 401');
	echo "{}";
}
?>