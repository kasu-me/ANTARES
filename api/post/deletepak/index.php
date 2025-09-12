<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD]){
	if( $_SERVER["REQUEST_METHOD"]!=="POST"){
		header('HTTP/1.0 405');
		echo "{}";
		exit();
	}
	if( ! isset($_POST["fileName"]) || $_POST["fileName"]=="" ){
		header('HTTP/1.0 400');
		echo '{"message":"ファイル名が入力されていません。","type":"error"}';
		exit();
	}

	$fileFullPath=$TEMPORARY_PAK_FILE_DIRECTORY_PATH."/".$_POST["fileName"];

	//pak追加申請リストファイルの読み込み
	$temporaryPakFileList=file_get_contents($TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH);
	$deleteTarget="";
	foreach (explode("\n",$temporaryPakFileList) as $key => $value) {
		$addedPakInfo=explode(",",$value);
		if(count($addedPakInfo)!=3){
			continue;
		}
		if($addedPakInfo[1]==$_POST["fileName"]){
			$deleteTarget=$value;
			break;
		}
	}
	if($deleteTarget==""){
		header('HTTP/1.0 404');
		echo '{"message":"指定されたファイルの申請情報が見つかりません。","type":"error"}';
		exit();
	}
	//申請リストから削除
	$temporaryPakFileList=str_replace($deleteTarget."\n","",$temporaryPakFileList);
	file_put_contents($TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH,$temporaryPakFileList);
	
	//ファイル削除
	exec("rm -rf ".$fileFullPath);

	//ZIPの場合ディレクトリも削除
	if(str_ends_with($_POST["fileName"],".zip")){
		$dir=substr($fileFullPath,0,-4);
		exec("rm -rf ".$dir);
	}

	//削除できた場合は管理者用チャンネルに通知を送信
	$discord_webhook_body_obj=json_decode(file_get_contents("template.json"));
	$discord_webhook_body_obj->username=$SIMUTRANS_SERVER_NAME." お知らせ";
	$discord_webhook_body_obj->avatar_url="https://".$_SERVER["SERVER_NAME"]."/img/discord_icon.png";	
	$discord_reply="";
	foreach ($ADMIN_DISCORD_USER_REPLY_TEXTS as $key => $value) {
		$discord_reply.=$value." ";
	}
	$discord_webhook_body_obj->content=$discord_reply;

	//Embedにフィールドを追加
	$fields=[[]];
	$fields[0]["inline"]=false;
	$fields[0]["name"]="対象ファイル";
	$fields[0]["value"]=$_POST["fileName"];
	$discord_webhook_body_obj->embeds[0]->fields=$fields;

	$discord_webhook_body_json=json_encode($discord_webhook_body_obj, JSON_UNESCAPED_UNICODE);

	//Webhook送信
	$ch = curl_init($DISCORD_WEBHOOK_URL_ADMIN_CHANNEL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER	, ['Content-Type: application/json']);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $discord_webhook_body_json);

	curl_exec($ch);
	if (curl_errno($ch)) {
		header('HTTP/1.0 500');
		echo '{"message":"管理者への通知に失敗しました。","type":"error"}';
	}
	curl_close($ch);
	
	header('HTTP/1.0 202');
	echo "done";
	exit();
}else{
	header('HTTP/1.0 401');
	echo "{}";
	exit();
}

?>