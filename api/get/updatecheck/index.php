<?php
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");

if(isLogIn() && $_SESSION[$SESSION_ID_DETERMINE_GUILD]){	
	//更新情報を取得
	exec($UPDATE_COMMENTS_GETTER_PATH,$comments_output);
	$obj=json_decode(implode("",$comments_output));
	
	//Webhook送信準備
	$discord_webhook_body_obj=json_decode(file_get_contents("template.json"));
	$discord_webhook_body_obj->username=$SIMUTRANS_SERVER_NAME." お知らせ";
	$discord_webhook_body_obj->avatar_url="https://".$_SERVER["SERVER_NAME"]."/img/discord_icon.png";	

	$commits=[];
	foreach ($obj as $key => $value) {
		$commit_message=$value->commit->message;
		array_push($commits,$commit_message);

		//Embedにフィールドを追加
		$field=[];
		$field["inline"]=false;
		$field["value"]="";
		$splitted_commit_message=explode("\n\n",$commit_message);
		if(count($splitted_commit_message)>1){
			$field["name"]="・".$splitted_commit_message[0];
			$field["value"]="--   ".$splitted_commit_message[1];
		}else{
			$field["name"]="・".$commit_message;
		}
		array_push($discord_webhook_body_obj->embeds[0]->fields,$field);
	}

	//変更がある場合Webhookを送信
	if (count($commits)>0){
		$discord_webhook_body_json=json_encode($discord_webhook_body_obj, JSON_UNESCAPED_UNICODE);

		$ch = curl_init($DISCORD_WEBHOOK_URL_NOTICE_CHANNEL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER	, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $discord_webhook_body_json);

		curl_exec($ch);
		if (curl_errno($ch)) {
			header('HTTP/1.0 500');
			echo "[]";
		}
		curl_close($ch);
	}

	echo json_encode($commits, JSON_UNESCAPED_UNICODE);

}else{
	header('HTTP/1.0 401');
	echo "[]";
}

?>