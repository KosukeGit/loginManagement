<?php
require 'Password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "hogeUser";  // ユーザー名
$db['pass'] = "hogehoge";  // ユーザー名のパスワード
$db['dbname'] = "loginManagement";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
	// 1. ユーザIDの入力チェック
	if (empty($_POST["username"])) {  // emptyは値が空のとき
		$errorMessage = 'ユーザーIDが未入力です。';
	} else if (empty($_POST["password"])) {
		$errorMessage = 'パスワードが未入力です。';
	} else if (empty($_POST["password2"])) {
		$errorMessage = 'パスワードが未入力です。';
	}

	if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]) {
		// 入力したユーザIDとパスワードを格納
		$username = $_POST["username"];
		$password = $_POST["password"];

		// 2. ユーザIDとパスワードが入力されていたら認証する
		$dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

		// 3. エラー処理
		try {
			$pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

			$stmt = $pdo->prepare("INSERT INTO userData(name, password) VALUES (?, ?)");

			$stmt->execute(array($username, password_hash($password, PASSWORD_DEFAULT)));  // 今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡す  // パスワードはちゃんとハッシュ化を行う
			$userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

			$signUpMessage = '登録が完了しました。あなたの登録IDは '. $userid. ' です。パスワードは '. $password. ' です。';  // ログイン時に使用する、登録したIDとパスワードを表示
		} catch (PDOException $e) {
			$errorMessage = 'データベースエラー';
			// $e->getMessage() でエラー内容を参照可能（デバック時のみ表示）
			// echo $e->getMessage();
		}
	} else if($_POST["password"] != $_POST["password2"]) {
		$errorMessage = 'パスワードに誤りがあります。';
	}
}
/*
MAMPのphpMyAdmin内

・データベース名：loginManagement

・テーブル名：userData
	・id (int(5))、primary key設定(id(隠し番号)の被りを無くすため)、AUTO_INCREMENT(id被り防止)
	・name (varchar(20))
	・password (varchar(100))  // ハッシュ化するから少し大きめ

・ユーザ
	・ユーザ：hogeUser
	・ホスト：localhost
	・パスワード：hogehoge
	・グローバル権限：全てにチェック(本来はデータベースに対して必要な権限だけを持つユーザを使うようにする)
*/

?>

<!doctype html>
<html>
	<head>
			<meta charset="UTF-8">
			<title>新規登録</title>
	</head>
	<body>
		<h1>新規登録画面</h1>
		<form id="loginForm" name="loginForm" action="" method="POST">
			<fieldset>  <!-- fieldsetはグループ化してくれる(線で囲ってくれる) -->
				<legend>新規登録フォーム</legend>  <!-- グループの先頭には、<LEGEND>～</LEGEND>で入力項目グループにタイトルをつけます。 -->
				<div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
				<div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>
				<label for="username">ユーザー名</label><input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">  <!-- 初回起動はユーザー名空白にして、２回目以降はPOST送信したユーザー名が保存されている。 -->
				<br>
				<label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
				<br>
				<label for="password2">パスワード(確認用)</label><input type="password" id="password2" name="password2" value="" placeholder="再度パスワードを入力">
				<br>
				<input type="submit" id="signUp" name="signUp" value="新規登録">
			</fieldset>
		</form>
		<br>
		<form action="Login.php">
			<input type="submit" value="戻る">
		</form>
	</body>
</html>

