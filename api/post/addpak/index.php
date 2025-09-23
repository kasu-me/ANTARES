<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

onlyAllowAuthenticated();

if( $_SERVER["REQUEST_METHOD"]!=="POST"){
	header('HTTP/1.0 405');
	echo "{}";
	exit();
}
if( ! isset($_FILES["uploadfile"]) ){
	header('HTTP/1.0 400');
	echo '{"message":"ファイルが添付されていません。","type":"error"}';
	exit();
}
if( ! isset($_POST["description"]) || $_POST["description"]=="" ){
	header('HTTP/1.0 400');
	echo '{"message":"説明が入力されていません。","type":"error"}';
	exit();
}
if(!str_ends_with($_FILES["uploadfile"]['name'],".zip")&&!str_ends_with($_FILES["uploadfile"]['name'],".pak")){
	header('HTTP/1.0 400');
	echo '{"message":"ファイルの種類がpakまたはzipではありません。","type":"error"}';
	exit();
}

$fileFullPath=$TEMPORARY_PAK_FILE_DIRECTORY_PATH."/".$_FILES["uploadfile"]['name'];

//一時置き場にアップロードされたファイルを配置
$result = move_uploaded_file(
	$_FILES["uploadfile"]['tmp_name'],
	$fileFullPath
);

//同じ名前のファイルが申請された場合は古い申請を削除
$temporaryPakFileList=file_get_contents($TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH);
$deleteTarget="";
foreach (explode("\n",$temporaryPakFileList) as $key => $value) {
	$addedPakInfo=explode(",",$value);
	if(count($addedPakInfo)!=3){
		continue;
	}
	if($addedPakInfo[1]==$_FILES["uploadfile"]['name']){
		$deleteTarget=$value;
		break;
	}
}
if($deleteTarget!=""){
	//申請リストから削除
	$temporaryPakFileList=str_replace($deleteTarget."\n","",$temporaryPakFileList);
	file_put_contents($TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH,$temporaryPakFileList);
}

//pak追加申請リストファイルに追記
file_put_contents($TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH,$_SESSION["name"].",".$_FILES["uploadfile"]['name'].",".$_POST["description"]."\n",FILE_APPEND);

//ファイル配置に成功した場合
if( $result !== false ){
	//ZIPの場合解凍
	if(str_ends_with($_FILES["uploadfile"]['name'],".zip")){
		$dir=substr($fileFullPath,0,-4);
		exec("mkdir ".$dir);
		exec("unzip -d ".$dir." -o ".$fileFullPath);
		chown($dir,$OS_USER_NAME);
		system("chown -R ".$OS_USER_NAME." ".$dir);
	}
	chown($fileFullPath,$OS_USER_NAME);
	system("chown ".$OS_USER_NAME." ".$fileFullPath);

	//追加できた場合は管理者用チャンネルに通知を送信
	$discord_webhook_body_obj=json_decode(file_get_contents("template.json"));
	$discord_webhook_body_obj->username=$SIMUTRANS_SERVER_NAME." お知らせ";
	$discord_webhook_body_obj->avatar_url="https://".$_SERVER["SERVER_NAME"]."/img/discord_icon.png";	
	$discord_reply="";
	foreach ($ADMIN_DISCORD_USER_REPLY_TEXTS as $key => $value) {
		$discord_reply.=$value." ";
	}
	$discord_webhook_body_obj->content=$discord_reply;

	//Embedにフィールドを追加
	$fields=[[],[],[]];
	$fields[0]["inline"]=false;
	$fields[0]["name"]="対象ファイル";
	$fields[0]["value"]=$_FILES["uploadfile"]['name'];
	$fields[1]["inline"]=false;
	$fields[1]["name"]="説明";
	$fields[1]["value"]=$_POST["description"];
	$fields[2]["inline"]=false;
	$fields[2]["name"]="申請者";
	$fields[2]["value"]=$_SESSION["name"];
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
	header('HTTP/1.0 500');
	echo "{}";
	exit();
}

?>