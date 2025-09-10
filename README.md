# ANTARESについて
Ahozura-Ns Total Administration Registration and Execution System
## これは何？
NetSimutransの運営者･参加者向けの各種操作のインタフェースをブラウザ上で提供するアプリケーションです。

認証･認可はDiscordのOAuth認証を利用しており、特定のサーバに入っているメンバのみ利用を許可することで、部外者の操作を排除します。
## 機能一覧
### 一般参加者向け機能
* サーバの死活確認
* 接続中のクライアント確認
* Pakセットのダウンロード
* 最新マップのダウンロード
* サーバが落ちていた場合の再起動
* 保存コマンドの送信
* Pak追加申請
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
1. 本リポジトリの中身を丸ごとダウンロードし、お使いのサーバに入れます。
2. settings_template.phpをコピーし、settings.phpという名称に変更します。
3. settings.phpの必要項目を入力します。
4. 多分これで使えます
## その他
* UIのフォントに[LINE Seed](https://seed.line.me/index_jp.html)を利用しています。このフォントは[SIL OPEN FONT LICENSE](https://licenses.opensource.jp/OFL-1.1/OFL-1.1.html)に基づき提供されており、著作権はLINEヤフー株式会社が有しています。
* アイコンフォントに[Segoe MDL2 Assets](https://learn.microsoft.com/ja-jp/windows/apps/design/style/segoe-ui-symbol-font)を利用しています。このフォントは[MITライセンス](https://opensource.org/license/mit)に基づき提供されており、著作権はMicrosoftが有しています。
