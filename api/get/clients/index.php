<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

onlyAllowAuthenticated();

//nettool接続
$nettool_command=$SIMUTRANS_NETTOOL." -p ".$SIMUTRANS_ADMIN_PASSWORD." clients 2>&1";
exec($nettool_command,$nettool_output);

//返却JSON
$result=[];
//固定値系
$result["ip"]=$SIMUTRANS_IP;
$result["pakLink"]=$PAK_LINK;
$result["pakVersion"]=file_get_contents($SIMUTRANS_PAK_VERSION_PATH);
$result["clients"]=[];

$isAlive=(strpos($nettool_output[1],"Could not connect to server") === false);
$inMaintenance=false;

if(!$isAlive){
	$ch = curl_init($SIMUTRANS_UNDER_MAINTENANCE_URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	if (!curl_errno($ch)) {
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE)!==404) {
			$inMaintenance=true;
		}
	}
	curl_close($ch);
}

$result["healthStatus"]=$isAlive?"alive":($inMaintenance?"maintenance":"dead");

if($result["healthStatus"]=="alive"){
	foreach ($nettool_output as $key => $value) {
		if(strpos($value,"..")!==false){
			array_push($result["clients"],trim(explode("..",$value)[1]));
		}
	}
}
echo json_encode($result);
?>