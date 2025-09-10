const CURRENT_URL = new URL(location.href);
const CURRENT_HOST = CURRENT_URL.host;

window.addEventListener("load", () => {
	//ローディング画面
	const loading = document.getElementById("loading");
	//メッセージ欄
	const messageArea = document.getElementById("message-area");

	//IPアドレスを表示する場所
	const nsIPArea = document.getElementById("NS-IP");
	//ステータスを表示する場所
	const nsStatusArea = document.getElementById("NS-Status");
	//接続人数を表示する場所
	const nsClientsListCount = document.getElementById("NS-Clients-count");
	//クライアントリストを表示する場所
	const nsClientsListArea = document.getElementById("NS-Clients-list");

	//Pakファイルダウンロードボタン
	const pakDownloadButton = document.getElementById("pak-download-link");
	//Pakバージョンを表示する場所
	const pakVersion = document.getElementById("pak-version");
	//再起動コマンド送信ボタン
	const rebootButton = document.getElementById("reboot-button");
	//保存コマンド送信ボタン
	const saveButton = document.getElementById("save-button");
	//再読み込みボタン
	const reloadButton = document.getElementById("reload-button");
	//強制停止コマンド送信ボタン
	const killButton = document.getElementById("kill-button");
	//管理者モードか否か(強制停止モードが存在するか否かで判定)
	const isAdminMode = killButton == null ? false : true;
	//pak追加申請ダイアログ表示ボタン
	const pakAddDialogOpenButton = document.getElementById("pak-add-dialog-open-button");
	//ファイル情報保持用のinput要素
	const inputFileBox = document.getElementById("input-file");
	//ファイル情報送信用のinput要素[type=submit]
	const pakAddSubmitButton = document.getElementById("pak-add-file-submit");
	//ファイル情報送信用のform要素
	const pakAddForm = document.getElementById("pak-add-file");
	//申請受理済pak一覧ダイアログ表示ボタン
	const pakAddListButton = document.getElementById("pak-add-list-button");

	//Pak追加申請ダイアログ

	//ファイル追加ボタン
	const fileAddButton = document.getElementById("file-add-button");
	//説明入力ボックス
	const addPakDescriptionBox = document.getElementById("add-pak-description-box");
	//選択したファイル名を表示する場所
	const addedFileNameDisplayArea = document.getElementById("added-file-name-display-area");
	//ファイル送信時エラーの表示場所
	const pakAddMessageArea = document.getElementById("pak-add-message-area");

	//申請受理済Pakファイル一覧ダイアログ

	//pakが存在しない場合のメッセージを表示する場所
	const pakNotExistsMessageArea = document.getElementById("pak-not-exists-message-area");
	//申請受理済pak一覧テーブル
	const pakAddListTable = document.getElementById("pak-add-list-table");

	function sendSimpleHttpRequest(url, method, body, successCallback, failedCallback) {
		const option = {
			method: method,
			headers: {
				"Accept": "application/json"
			}
		};
		if (method != "GET") {
			option.body = body;
		}
		fetch(url, option).then(response => {
			if (response.ok) {
				response.text().then(successCallback);
			} else {
				response.text().then(
					text => {
						failedCallback(response.status, text)
					}
				);
			}
		}).catch(error => {
			showMessage(`通信に失敗しました。ネットワークの状態を確認してください。(エラー内容:${error})`, "error");

			//ローディング画面を終了する
			loading.classList.add("off");

			//リロードボタンを有効化し、それ以外のボタンを無効化する
			reloadButton.classList.remove("disabled");
			reloadButton.classList.remove("rotating");
			saveButton.classList.add("disabled");
			rebootButton.classList.add("disabled");
			//本体の応答がないせいで通信失敗している可能性があるため、強制停止ボタンは無効化しない
			killButton?.classList?.remove("disabled");
		});
	}

	//nettoolからclients情報を取得
	function sendGetClientsRequestToNettool() {
		nsStatusArea.parentNode.classList.remove("is-dead");
		nsStatusArea.parentNode.classList.remove("is-maintenance");
		nsStatusArea.classList.remove("icon-bef");
		nsStatusArea.innerText = "取得中...";
		reloadButton.classList.add("disabled");
		reloadButton.classList.add("rotating");
		nsClientsListArea.innerHTML = "";
		nsClientsListCount.innerText = "";
		loading.classList.remove("off");

		//apiにリクエストを送信
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/get/clients`, "GET", null, (text) => {
			const result = JSON.parse(text);

			//接続先欄に値を投入
			nsIPArea.innerText = result.ip;

			//接続者数欄に値を投入
			nsClientsListCount.innerText = `${result.clients.length}名が接続中`;

			//死活状態判定
			if (result.healthStatus == "alive") {
				//稼働中の場合
				nsStatusArea.innerText = "稼働中";
				nsStatusArea.classList.add("icon-bef");
				nsStatusArea.setAttribute("icon", "");
				saveButton.classList.remove("disabled");
				rebootButton.classList.add("disabled");
				killButton?.classList?.remove("disabled");

				//接続者
				result.clients.forEach(client => {
					const li = document.createElement("li");
					li.innerText = client;
					nsClientsListArea.append(li);
				});
			} else if (result.healthStatus == "dead") {
				//停止中の場合
				nsStatusArea.innerText = "停止中";
				nsStatusArea.classList.add("icon-bef");
				nsStatusArea.setAttribute("icon", "");
				nsStatusArea.parentNode.classList.add("is-dead");
				saveButton.classList.add("disabled");
				rebootButton.classList.remove("disabled");
				killButton?.classList?.add("disabled");
			} else if (result.healthStatus == "maintenance") {
				//メンテナンス中の場合
				nsStatusArea.innerText = "メンテナンス中";
				nsStatusArea.classList.add("icon-bef");
				nsStatusArea.setAttribute("icon", "");
				nsStatusArea.parentNode.classList.add("is-maintenance");
				saveButton.classList.add("disabled");
				rebootButton.classList.add("disabled");
				killButton?.classList?.add("disabled");
			}

			//pakダウンロードボタンに値を設定し有効化
			pakDownloadButton.href = result.pakLink;
			pakDownloadButton.classList.remove("disabled");
			pakVersion.innerText = result.pakVersion;

			//ローディング画面を終了しリロードボタンを有効化
			loading.classList.add("off");
			reloadButton.classList.remove("disabled");
			reloadButton.classList.remove("rotating");
		}, (status) => {
			judgeByHTTPStatus(status);
		});
	}

	//最新の情報に更新
	reloadButton.addEventListener("click", () => {
		clearMessage();
		sendGetClientsRequestToNettool();
	});

	//NS再起動
	rebootButton.addEventListener("click", () => {
		loading.classList.remove("off");
		rebootButton.classList.add("disabled");
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/get/reboot`, "GET", null, (text) => {
			//サーバの起動処理が終わるまで10秒待つ
			//(起動前にsendGetCklientsRequestToNettoolを送るとタイムアウトする可能性があるため)
			setTimeout(() => {
				saveButton.classList.remove("disabled");
				loading.classList.add("off");
				sendGetClientsRequestToNettool();
			}, 10000);
		}, (status) => {
			judgeByHTTPStatus(status);
		});
		//サーバの起動処理と並行して、Discordへのお知らせ投稿を行う
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/get/updatecheck`, "GET", null, (text) => {
			const count = JSON.parse(text).length;
			if (count > 0) {
				showMessage(`Discordに更新内容のお知らせを投稿しました。(アップデート数:${count}件)`, "info");
			}
		}, (status) => {
			judgeByHTTPStatus(status);
		});
	});

	//保存コマンド送信
	saveButton.addEventListener("click", () => {
		loading.classList.remove("off");
		saveButton.classList.add("disabled");
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/get/save`, "GET", null, (text) => {
			saveButton.classList.remove("disabled");
			loading.classList.add("off");
		}, (status) => {
			judgeByHTTPStatus(status);
		});
	});

	//サーバを強制終了(管理者によるログイン時のみ)
	killButton?.addEventListener("click", () => {
		if (!isAdminMode) return;
		loading.classList.remove("off");
		killButton.classList.add("disabled");
		const confirmResult = confirm("本当に強制停止してもよろしいですか？サーバを停止すると全ての接続ユーザに影響し、データが失われる可能性があります。");
		if (confirmResult) {
			sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/get/kill`, "GET", null, (text) => {
				killButton.classList.remove("disabled");
				loading.classList.add("off");
				sendGetClientsRequestToNettool();
			}, (status) => {
				judgeByHTTPStatus(status);
			});
		} else {
			killButton.classList.remove("disabled");
			loading.classList.add("off");
		}
	});

	//ファイル追加ダイアログを定義
	new Dialog("pakAddDialog", `Pak追加申請`, document.querySelectorAll("#dialog-content>.pak-add-dialog>*"), [{ "content": "申請", "event": `Dialog.list.pakAddDialog.functions.sendForm();`, "icon": "", "class": "icon-bef", "id": "application-button" }, { "content": "キャンセル", "event": `Dialog.list.pakAddDialog.off();`, "icon": "", "class": "icon-bef red" }], {
		display: () => {
			inputFileBox.value = "";
			addPakDescriptionBox.value = "";
			addPakDescriptionBox.classList.remove("has-error");
			addedFileNameDisplayArea.innerText = "ファイルが選択されていません";
			applicationButton.classList.add("disabled");
			pakAddMessageArea.innerHTML = "";
			Dialog.list.pakAddDialog.on();
		},
		sendForm: () => {
			pakAddSubmitButton.click();
		}
	}, true);
	const applicationButton = Dialog.list.pakAddDialog.buttons.querySelector("#application-button");

	//Pak追加申請ダイアログを開く
	pakAddDialogOpenButton.addEventListener("click", () => {
		Dialog.list.pakAddDialog.functions.display();
	});

	//ファイル選択ダイアログを開く
	fileAddButton.addEventListener("click", () => {
		inputFileBox.click();
	});
	inputFileBox.addEventListener("change", () => {
		const fileName = inputFileBox.files[0]?.name;
		addedFileNameDisplayArea.innerText = fileName ?? "ファイルが選択されていません";
		if (fileName == null) {
			applicationButton.classList.add("disabled");
		} else {
			applicationButton.classList.remove("disabled");
		}
	});

	//ファイル送信
	pakAddForm.addEventListener("submit", (e) => {
		e.preventDefault();
		loading.classList.remove("off");
		applicationButton.classList.add("disabled");
		addPakDescriptionBox.classList.remove("has-error");

		const formData = new FormData();
		formData.append("description", addPakDescriptionBox.value);
		formData.append("uploadfile", inputFileBox.files[0]);
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/post/addpak/`, "POST", formData, (text) => {
			loading.classList.add("off");
			applicationButton.classList.remove("disabled");
			showMessage(`Pak追加申請を送信しました。(対象ファイル:${inputFileBox.files[0].name})`, "info");
			Dialog.list.pakAddDialog.off();
		}, (status, text) => {
			judgeByHTTPStatus(status, text, true, pakAddMessageArea);
			applicationButton.classList.remove("disabled");
			if (status == 400 && JSON.parse(text).message.startsWith("説明")) {
				addPakDescriptionBox.classList.add("has-error");
				addPakDescriptionBox.focus();
			}
		});

	});

	//申請受理済Pak一覧ダイアログを定義
	new Dialog("pakAddListDialog", `申請受理済Pak一覧`, document.querySelectorAll("#dialog-content>.pak-add-list-dialog>*"), [{ "content": "OK", "event": `Dialog.list.pakAddListDialog.off();`, "icon": "", "class": "icon-bef" }], {
		display: (text) => {
			const queues = JSON.parse(text);
			pakAddListTable.innerHTML = "";
			const template = document.querySelector("template").content.cloneNode(true);
			pakAddListTable.append(template.querySelector("thead"));
			queues.list.forEach((addedPakData, i) => {
				const tr = template.querySelector("tr.pak-list").cloneNode(true);
				tr.querySelector(".number").innerText = i + 1;
				tr.querySelector(".file-name").innerText = addedPakData.fileName;
				tr.querySelector(".description").innerText = addedPakData.description;
				tr.querySelector(".author").innerText = addedPakData.author;
				const deleteButton = tr.querySelector(".controll button.delete-button");
				deleteButton.addEventListener("click", () => {
					if (confirm(`本当に申請を取り下げてよろしいですか？この操作は取り消せません。(対象ファイル:${addedPakData.fileName})`)) {
						deletePakList(addedPakData.fileName, deleteButton);
					}
				});
				pakAddListTable.append(tr);
			});
			pakNotExistsMessageArea.innerHTML = "";
			if (queues.list.length == 0) {
				pakNotExistsMessageArea.innerText = `現在、申請されているPakファイルはありません`;
				pakAddListTable.classList.add("not-displayed");
			} else {
				pakAddListTable.classList.remove("not-displayed");
			}

			Dialog.list.pakAddListDialog.on();
		}
	}, true);

	//申請受理済Pak一覧ダイアログを開く
	pakAddListButton.addEventListener("click", () => {
		pakAddListButton.classList.add("disabled");
		loading.classList.remove("off");
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/get/paklist`, "GET", null, (text) => {
			pakAddListButton.classList.remove("disabled");
			loading.classList.add("off");
			Dialog.list.pakAddListDialog.functions.display(text);
		}, (status) => {
			judgeByHTTPStatus(status);
			pakAddListButton.classList.remove("disabled");
		});
	});

	//申請取り下げ
	function deletePakList(fileName, deleteButton) {
		deleteButton.classList.add("disabled");
		loading.classList.remove("off");
		const formData = new FormData();
		formData.append("fileName", fileName);
		sendSimpleHttpRequest(`https://${CURRENT_HOST}/api/post/deletepak/`, "POST", formData, (text) => {
			showMessage(`Pak追加申請を取り下げました。(対象ファイル:${fileName})`, "info");
			deleteButton.classList.remove("disabled");
			loading.classList.add("off");
			Dialog.list.pakAddListDialog.off();
		}, (status, text) => {
			judgeByHTTPStatus(status, text);
			deleteButton.classList.remove("disabled");
			Dialog.list.pakAddListDialog.off();
		});
	}

	//HTTPステータスコードによる処理振り分け
	function judgeByHTTPStatus(httpStatus, messageJSON, isNotAllDisableButtons, target) {
		const messageObject = (messageJSON !== undefined || messageJSON !== null) ? ((isValidJson(messageJSON) && JSON.parse(messageJSON).message != undefined) ? JSON.parse(messageJSON) : null) : null;
		if (messageObject === null) {
			switch (httpStatus) {
				case 401:
					//認証エラー
					showMessage(`認証に失敗しました。ログインし直してください。(HTTPステータスコード:${httpStatus})`, "error", target);
					break;
				case 500:
					//サーバエラー
					showMessage(`サーバでエラーが発生しました。時間をおいて再度お試しください。解決しない場合、管理者に連絡してください。(HTTPステータスコード:${httpStatus})`, "error", target);
					break;
				case 400:
					//Bad Requestエラー
					showMessage(`リクエストが不正です。(HTTPステータスコード:${httpStatus})`, "error", target);
					break;
				case 413:
					//Content Too Largeエラー
					showMessage(`ファイルのサイズが大きすぎます。(HTTPステータスコード:${httpStatus})`, "error", target);
					break;
				default:
					//その他のエラー
					showMessage(`予期しないエラーが発生しました。管理者に連絡してください。(HTTPステータスコード:${httpStatus})`, "error", target);
					break;
			}
		} else {
			showMessage(`${messageObject.message}(HTTPステータスコード:${httpStatus})`, messageObject.type, target);
		}
		//ローディング画面を終了する
		loading.classList.add("off");
		//リロードボタンを有効化し、それ以外のボタンを無効化する
		reloadButton.classList.remove("disabled");
		reloadButton.classList.remove("rotating");
		if (!isNotAllDisableButtons) {
			saveButton.classList.add("disabled");
			rebootButton.classList.add("disabled");
			killButton?.classList?.add("disabled");
		}
	}

	//メッセージ表示
	function showMessage(message, messageType, target) {
		target = target ?? messageArea;

		const template = document.querySelector("template").content.cloneNode(true);

		const messageDiv = template.querySelector(".message");
		messageDiv.classList.add(`message-${messageType}`);

		const messageContent = messageDiv.querySelector(".message-content");
		messageContent.innerText = message;

		const closeButton = messageDiv.querySelector(".message-close-button")
		closeButton.addEventListener("click", () => {
			messageDiv.remove();
		});

		target.append(messageDiv);
	}

	//メッセージをクリア
	function clearMessage() {
		messageArea.innerHTML = "";
	}

	//JSONとして有効かどうか判定
	function isValidJson(text) {
		try {
			JSON.parse(text)
		} catch (e) {
			return false;
		}
		return true;
	}

	//初回起動時に情報を取得
	sendGetClientsRequestToNettool();
});