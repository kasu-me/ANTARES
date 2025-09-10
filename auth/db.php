<?php
function sendSQL($query,$params){
	include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
	$connection = pg_connect("host=".$DB_HOST." port=".$DB_PORT." dbname=".$DB_NAME." user=".$DB_USER." password=".$DB_PASSWORD);
	if (pg_connection_status($connection) === PGSQL_CONNECTION_BAD) {
		//echo "aaa接続できません";
		die("データベースに接続できません:");
	}else{
		//echo "接続成功";
	}
	$result = pg_query_params($connection, $query, $params);
	if($result){
		return $result;
	}else{
		die("接続できません");
	}
	pg_close($connection);
}


?>