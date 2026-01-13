<?php 
session_start();
session_regenerate_id();
include($_SERVER["DOCUMENT_ROOT"]."/settings.php");
include($_SERVER["DOCUMENT_ROOT"]."/common/common.php");
include($_SERVER["DOCUMENT_ROOT"]."/auth/common.php");
$VERSION="1.0.7";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<title>ANTARES v<?php echo $VERSION; ?></title>
<meta charset="utf-8">
<link rel="shortcut icon" href="/img/favicon.png" />
<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
<link rel="stylesheet" href="/fonts/font.css<?php echo "?ver=".filemtime($_SERVER['DOCUMENT_ROOT']."/fonts/font.css") ?>">
<link rel="stylesheet" href="/css/animation.css<?php echo "?ver=".filemtime($_SERVER['DOCUMENT_ROOT']."/css/animation.css") ?>">
<link rel="stylesheet" href="/css/main.css<?php echo "?ver=".filemtime($_SERVER['DOCUMENT_ROOT']."/css/main.css") ?>">
<link rel="stylesheet" href="/css/dialog.css<?php echo "?ver=".filemtime($_SERVER['DOCUMENT_ROOT']."/css/dialog.css") ?>">
<?php
if(isLoginAndMember()){
?>
<script src="/js/main.js<?php echo "?ver=".filemtime($_SERVER['DOCUMENT_ROOT']."/js/main.js") ?>"></script>
<script src="/js/dialog.js<?php echo "?ver=".filemtime($_SERVER['DOCUMENT_ROOT']."/js/dialog.js") ?>"></script>
<?php } ?>
</head>
<body>
	<header>
		<div>
			<h1>ANTARES<span class="version">v<?php echo $VERSION; ?></span></h1>
			<h2>Ahozura-Ns Total Administration Registration and Execution System</h2>
		</div>
		<?php
		if(isLoginAndMember()){
		?>
			<div class="user-info">
				<div class="icon-bef" icon=""><?php 
				echo $_SESSION["name"];
				?><span class="user-role"><?php 
				if(isAdmin()){
					echo "Administrator"; 
				}else{
					echo "Guest"; 
				}
				?></span></div>
				<div><a href="/auth/discord_login.php?action=logout" class="icon-bef link-button" icon="">ログアウト</a></div>
			</div>
		<?php
		}
		?>
	</header>
	<div id="message-area">
	<?php
	if(!isLogIn()){
	?>
		<div class="message message-warn"><span class="message-content">本システムのご利用にはログインが必要です。</span></div>
		<div class="foot-buttons">
			<a href="/auth/discord_login.php?action=login" class="icon-bef icon-rotate-right-90deg link-button discord" icon="">Discord認証によるログイン</a>
		</div>
	<?php }	else if(isLogIn() && !$_SESSION[$SESSION_ID_DETERMINE_GUILD]){
	?>
		<div class="message message-warn"><span class="message-content">お使いのDiscordアカウントで本システムを利用することはできません。管理者に問い合わせてください。</span></div>
		<div class="foot-buttons"><a href="/auth/discord_login.php?action=logout" class="icon-bef link-button" icon="">ログアウト</a></div>
	<?php } ?>
	</div>
	<main>
		<?php
		if(isLoginAndMember()){
		?>
		<div class="loaderparent" id="loading"><div class="loader"></div></div>
		<div id="buttons-container" class="main-panels">
			<button class="icon-bef" icon="" id="reload-button"">最新の情報に更新</button>
		</div>
		<div class="tiles main-panels">
			<h4 class="icon-bef" icon="">IP Address</h4>
			<div id="NS-IP"></div>
		</div>
		<div id="tiles-container" class="main-panels">
			<div class="tiles">
				<h4 class="icon-bef" icon="">Health</h4>
				<div id="NS-Status-container"><span id="NS-Status"></span></div>
			</div>
			<div class="tiles">
				<h4 class="icon-bef" icon="">Clients</h4>
				<div id="NS-Clients-count"></div>
				<ul id="NS-Clients-list"></ul>
			</div>
			<div class="tiles">
				<h4 class="icon-bef" icon="">Paks</h4>
				<div>
					<a id="pak-download-link" class="link-button disabled"><span class="icon-bef" icon="">ダウンロード (Ver.<span id="pak-version"></span>)</span></a>
				</div>
			</div>
			<div class="tiles">
				<h4 class="icon-bef" icon="">Latest Map</h4>
				<div>
					<a href="/api/get/savedata/" id="sve-download-link" class="link-button" download="server13353-network.sve"><span class="icon-bef" icon="">ダウンロード</span></a>
				</div>
			</div>
		</div>
		<div id="controller-container" class="main-panels">
			<div>
				<button class="icon-bef disabled" icon="" id="reboot-button"><?php echo $SIMUTRANS_SERVER_NAME; ?>を起動</button>
			</div>
			<div>
				<button class="icon-bef disabled" icon="" id="save-button">保存コマンドを送信</button>
			</div>
			<div>
				<button class="icon-bef" icon="" id="pak-add-dialog-open-button">Pak追加申請</button>
			</div>
			<div>
				<button class="icon-bef" icon="" id="pak-add-list-button">申請受理済Pak一覧</button>
			</div>
		</div>
		<div id="file-uploader-container" class="main-panels">
		</div>
		<?php 
		if(isAdmin()){
		?>
		<div class="main-panels">
			<h4 class="icon-bef" icon="">管理者用操作</h5>
			<div>
				<button class="icon-bef disabled red" icon="" id="kill-button"><?php echo $SIMUTRANS_SERVER_NAME; ?>を強制停止</button>
			</div>
		</div>
		<?php
		}
		?>
		<?php
		}
		?>
	</main>
	<footer>
		<div>&copy;2025 AhozuraNS Project</div>
		<p><span><a href="https://github.com/kasu-me/ANTARES/blob/main/README.md" target="_blank">About ANTARES</a></span><span><a href="https://github.com/kasu-me/ANTARES/releases" target="_blank">Releases</a></span></p>
	</footer>
	<form id="pak-add-file"><input type="file" id="input-file" name="uploadfile" accept=".zip,.pak"><input type="submit" id="pak-add-file-submit"></form>
	<template>
		<thead>
			<tr>
				<th>番号</th>
				<th>ファイル名</th>
				<th>説明</th>
				<th>申請者</th>
				<th>操作</th>
			</tr>
		</thead>
		<tr class="pak-list">
			<td class="number"></td><td class="file-name"></td><td class="description"></td><td class="author"></td><td class="controll"><button class="icon delete-button red"></button></td>
		</tr>
		<div class="message">
			<div class="message-content"></div><div class="message-close-button icon"></div>
		</div>
	</template>
	<div id="dialog-content" class="not-displayed">
		<div class="pak-add-dialog">
			<div id="pak-add-message-area" class="dialog-message-area"></div>
			<div><button id="file-add-button" class="icon-bef" icon="">ファイルを追加</button><span id="added-file-name-display-area"></span></div>
			<div><input id="add-pak-description-box" placeholder="説明"></div>
		</div>
		<div class="pak-add-list-dialog">
			<div id="pak-add-list-container"><table id="pak-add-list-table"></table></div>
			<div id="pak-not-exists-message-area"></div>
		</div>
	</div>
</body>
</html>
