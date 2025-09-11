<?php
//このファイルをコピーしてsettings.phpを作成し、各種項目を貴方の環境に合わせて編集してください。

//Simmutransサーバ名
$SIMUTRANS_SERVER_NAME="NETSIMUTRANS";

//Simutransサーバのパス設定
$SIMUTRANS_DIR="/home/example/simutrans";//Simutransサーバのディレクトリ
$SIMUTRANS_BIN=$SIMUTRANS_DIR."/sim";//Simutrans実行ファイルのパス
$SIMUTRANS_SAVEDATA_PATH=$SIMUTRANS_DIR."/server13353-network.sve";//Simutransのセーブデータのパス
$SIMUTRANS_NETTOOL=$SIMUTRANS_DIR."/nettool";//nettoolのパス

//Simutrans起動設定
$SIMUTRANS_SERVER_PORT="13353";
$SIMUTRANS_PAKSET="pak128japan";
$SIMUTRANS_ADMIN_PASSWORD="examplepassword";
$SIMUTRANS_LANG="ja";
$SIMUTRANS_IP="192.0.2.1:".$SIMUTRANS_SERVER_PORT;
$SIMUTRANS_LOG_PATH=$SIMUTRANS_DIR."/simutrans.log";//サーバのログ出力先

//pakset更新関連
$SIMUTRANS_PAK_VERSION_PATH="https://example.com/simutrans/updates.txt";//paksetのバージョンを記載したテキストファイルのURL
$SIMUTRANS_UNDER_MAINTENANCE_URL="https://example.com/simutrans/under_maintenance";//このURLが404以外を返す場合はメンテナンス中とみなす
$PAK_LINK="https://example.com/simutrans/paksets.zip";//paksetのダウンロードリンク

//pak追加申請関連
$TEMPORARY_PAK_FILE_DIRECTORY_PATH="/home/example/pak/files";//追加申請されたpakを一時的に置いておくディレクトリのパス
$TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH="/home/example/pak/pak-list.csv";

//Discord OAuth2設定
$DISCORD_OAUTH2_CLIENT_ID="9999999999999999999";
$DISCORD_OAUTH2_CLIENT_SECRET="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$DISCORD_GUILD_ID="999999999999999999";//このIDのサーバに参加しているユーザのみログインを許可
$DISCORD_GUILD_NAME="servername";//サーバ名。任意の文字列
$SESSION_ID_DETERMINE_GUILD="isjoined".$DISCORD_GUILD_NAME;

//Discord Webhook
$ADMIN_DISCORD_USER_IDS=["<@999999999999999999>"];//管理者のDiscordアカウントID
$DISCORD_WEBHOOK_URL_NOTICE_CHANNEL="https://discord.com/api/webhooks/9999999999999999999/XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";//お知らせチャンネル投稿用
$DISCORD_WEBHOOK_URL_ADMIN_CHANNEL="https://discord.com/api/webhooks/9999999999999999999/XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";//管理者用チャンネル投稿用

//更新内容取得用プログラムのパス
$UPDATE_COMMENTS_GETTER_PATH="/home/example/getchanges";//実行すると変更内容の配列がJSONで返ってくるプログラムを設定すること

//DB設定
$DB_HOST="localhost";
$DB_PORT="9999";
$DB_NAME="auth";
$DB_USER="postgres";
$DB_PASSWORD="xxxx";

//管理者リスト(Discordのユーザ名で指定)
$ADMIN_USER_NAMES=["yourname"];

//OSのユーザ名
$OS_USER_NAME="example";
?>