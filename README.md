# ANTARESについて
Ahozura-Ns Total Administration Registration and Execution System
## これは何？
NetSimutransの運営者･参加者向けの各種操作のインタフェースをブラウザ上で提供するアプリケーションです。

認証･認可はDiscordのOAuth認証を利用しており、特定のサーバに入っているメンバのみ利用を許可することで、部外者による操作を排除します。
## 機能一覧
ログインしたユーザには一般参加者または管理者のどちらかの権限が与えられます。管理者には任意の人物(複数可)を指定することができます。管理者は全ての機能にアクセス可能ですが、一般参加者はサーバ管理者向け機能にアクセスすることはできません。
### 全ユーザ向け機能
* サーバの死活確認
* 接続中のクライアント確認
* Pakセットのダウンロード
* 最新マップのダウンロード
* サーバが落ちていた場合の再起動
* 保存コマンドの送信
* Pak追加申請
  ** 追加申請の管理者への通知
* Pak追加申請取下
### サーバ管理者向け機能
* サーバ強制停止
## 前提条件
* NetSimutransが稼働しているLinux上でWebサーバが稼働していること
  * 上記環境にPHP8がインストールされていること
  * 上記環境にPostgreSQLがインストールされていること
* [Discord Developer Portal](https://discord.com/developers/applications)より、OAuth認証が利用できるアプリを作成していること
  * 上記アプリに「名前を読み取る」「参加サーバを読み取る」権限が付与されていること
* 1つ以上のDiscord Webhookを作成していること
## インストール方法
ANTARESはあなたのNetSimutransサーバにも導入することができます。
1. [Releases](https://github.com/kasu-me/ANTARES/releases)より本リポジトリの中身を丸ごとダウンロードし、お使いのサーバに入れます。
2. settings_template.phpをコピーし、settings.phpという名称に変更します。
3. settings.phpの必要項目を入力します。(詳細は後述)
4. 多分これで使えます
## setting.phpの編集
以下の変数を貴方の環境に合わせて編集してください。
### 共通事項
編集の際には、以下の事項を遵守してください。
* ディレクトリのパスを指定する場合、末尾に「/」を付与しないでください。
### Simmutransサーバ関連
#### $SIMUTRANS_SERVER_NAME
サーバ名称です。任意の名称を指定してください。何を指定しても動作は変わりません。ただ表示される文言が変わります。
#### $SIMUTRANS_DIR
Simutransの実行ファイルが配置されているディレクトリパスを指定してください。
#### $SIMUTRANS_BIN
Simutransの実行ファイルのパスを指定してください。通常は`$SIMUTRANS_DIR."/sim"`であるはずです。
#### $SIMUTRANS_SAVEDATA_PATH
サーバ上で保存されるデフォルトのセーブデータのパスを指定してください。通常は`$SIMUTRANS_DIR."/server13353-network.sve"`であるはずです。
#### $SIMUTRANS_NETTOOL
Simutransを操作するためのツールであるnettoolのパスを指定してください。通常は`$SIMUTRANS_DIR."/nettool"`であるはずです。
### Simutrans起動関連
#### $SIMUTRANS_SERVER_PORT
Net Simutransのポート番号を指定してください。
#### $SIMUTRANS_PAKSET
Pakセット名を指定してください。
#### $SIMUTRANS_ADMIN_PASSWORD
Net Simutransを起動する際に設定するパスワードを指定してください。
#### $SIMUTRANS_LANG
Net Simutransを起動する際の言語を指定してください。日本語であれば`ja`です。
#### $SIMUTRANS_IP
サーバのIPアドレスを指定してください。末尾に`":".$SIMUTRANS_SERVER_PORT`を付けて、ポート番号を含む完全なIPアドレスを指定してください。
#### $SIMUTRANS_LOG_PATH
Simutrans実行時のログを出力するファイルのパスを指定してください。

### Pakセット更新関連
#### $SIMUTRANS_PAK_VERSION_PATH
Pakセットのバージョンが記載されたテキストファイルのパスを指定してください。
#### $SIMUTRANS_UNDER_MAINTENANCE_URL
Simutransがメンテナンス中であるかどうかを確認するためのURLを指定してください。ここに指定されたURLが404以外を返す場合(リソースが存在する場合)、メンテナンス中であると見做されます。
#### $PAK_LINK
PakファイルをダウンロードするためのURLを指定してください。
### Pak追加申請関連
#### $TEMPORARY_PAK_FILE_DIRECTORY_PATH
メンバーから追加の申請がなされたファイルを保管しておくディレクトリのパスを指定してください。
#### $TEMPORARY_PAK_FILE_LIST_CSV_FILE_PATH
メンバーから追加の申請がなされたファイルについての情報をCSV形式で記録するファイルのパスを指定してください。
### Discord OAuth2関連
#### $DISCORD_OAUTH2_CLIENT_ID
ご自身で作成したDiscordアプリのクライアントIDを指定してください。
#### $DISCORD_OAUTH2_CLIENT_SECRET
ご自身で作成したDiscordアプリのクライアントシークレットを指定してください。この情報は慎重に扱ってください。
#### $DISCORD_GUILD_ID
DiscordサーバのIDを指定してください。ここで指定されたサーバに参加しているメンバーのみANTARESの操作が許可されます。
#### $DISCORD_GUILD_NAME
Discordサーバの名称を指定してください。任意の文字列で構いません。
#### $SESSION_ID_DETERMINE_GUILD
Discordサーバに参加している人物であるかを記録するためのセッションIDです。通常は`"isjoined".$DISCORD_GUILD_NAME`であるはずです。

### Discord Webhook関連
#### $ADMIN_DISCORD_USER_IDS
配列形式で、DiscordのアカウントIDを指定してください。管理者向けの通知がある場合にDiscordで送信される通知メッセージに、ここで指定したアカウントへのリプライが付けられます。

`$ADMIN_USER_NAMES`で指定したユーザと同一である必要はありません。
#### $DISCORD_WEBHOOK_URL_NOTICE_CHANNEL
DiscordのWebhookのURLを指定してください。ここで指定されたURLに、参加者全員向けのお知らせが投稿されます。
#### $DISCORD_WEBHOOK_URL_ADMIN_CHANNEL
DiscordのWebhookのURLを指定してください。ここで指定されたURLに、管理者向けの通知が投稿されます。
### 更新内容取得関連
#### $UPDATE_COMMENTS_GETTER_PATH
Pakセットのバージョン(`$SIMUTRANS_PAK_VERSION_PATH`で指定されたファイルの内容)が変更された場合、変更内容を取得するためのプログラムが動作しているパスを指定してください。
### DB関連
#### $DB_HOST
DBホスト名を指定してください。通常は`"localhost"`であるはずです。
#### $DB_PORT
DBサーバが動作するポート番号を指定してください。
#### $DB_NAME
DB名を指定してください。
#### $DB_USER
DBユーザ名を指定してください。
#### $DB_PASSWORD
DBパスワードを指定してください。
### ユーザ権限関連
#### $ADMIN_USER_NAMES
配列形式です。ANTARES上で管理者権限を与えたいユーザの名前をDiscordのユーザ名で指定してください。複数人を指定することも可能です。指定する人数に上限はありませんが、管理者権限を与えるに足る人物かしっかり検討してから追加するようにしてください。

`$ADMIN_DISCORD_USER_IDS`で指定したユーザと同一である必要はありません。
#### $OS_USER_NAME
OSのユーザ名を指定してください。ここで指定されたユーザに、各種ファイルの操作権限を与えます。
## リリース履歴
v1.0.5以降のリリース履歴は[Releases](https://github.com/kasu-me/ANTARES/releases)から参照してください。
### v1.0.0
* 暫定リリース
### v1.0.1
* 強制停止機能追加
### v1.0.2
* 再起動･アップデート内容投稿機能追加
### v1.0.3
* Pak追加申請機能追加
### v1.0.4
* 申請受理済みファイル一覧閲覧機能追加
## その他
* UIのフォントに[LINE Seed](https://seed.line.me/index_jp.html)を利用しています。このフォントは[SIL OPEN FONT LICENSE](https://licenses.opensource.jp/OFL-1.1/OFL-1.1.html)に基づき提供されており、著作権はLINEヤフー株式会社が有しています。
* アイコンフォントに[Segoe MDL2 Assets](https://learn.microsoft.com/ja-jp/windows/apps/design/style/segoe-ui-symbol-font)を利用しています。このフォントは[MITライセンス](https://opensource.org/license/mit)に基づき提供されており、著作権はMicrosoftが有しています。
