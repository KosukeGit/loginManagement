<?php
require 'Password.php';   // password_verfy()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "hogeUser";  // ユーザー名
$db['pass'] = "hogehoge";  // ユーザー名のパスワード
$db['dbname'] = "loginManagement";  // データベース名

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
	// 1. ユーザIDの入力チェック
	if (empty($_POST["userid"])) {  // emptyは値が空のとき
		$errorMessage = 'ユーザーIDが未入力です。';
	} else if (empty($_POST["password"])) {
		$errorMessage = 'パスワードが未入力です。';
	}

	if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
		// 入力したユーザIDを格納
		$userid = $_POST["userid"];

		// 2. ユーザIDとパスワードが入力されていたら認証する
		$dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

		// 3. エラー処理
		try {
			$pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

			$stmt = $pdo->prepare('SELECT * FROM userData WHERE id = ?');
			$stmt->execute(array($userid));

			$password = $_POST["password"];

			if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				//$db_hashed_pwd = $row['password'];
				//$passwordHash = password_hash($db_hashed_pwd, PASSWORD_DEFAULT);
				if (password_verify($password, $row['password'])) {
					//$errorMessage = $password. '/'. $row['password'];  // 確認用
					session_regenerate_id(true);

					// 入力したIDのユーザー名を取得
					$id = $row['id'];
					$sql = "SELECT * FROM userData WHERE id = $id";  //入力したIDからユーザー名を取得
					$stmt = $pdo->query($sql);
					foreach ($stmt as $row) {
						$row['name'];  // ユーザー名
					}
					$_SESSION["NAME"] = $row['name'];
					header("Location: Main.php");  // メイン画面へ遷移
					exit();  // 処理終了
				} else {
					// 認証失敗
					$errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
				}
			} else {
				// 4. 認証成功なら、セッションIDを新規に発行する
				// 該当データなし
				$errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
			}
		} catch (PDOException $e) {
			//$errorMessage = 'データベースエラー';
			$errorMessage = $sql;
			// $e->getMessage() でエラー内容を参照可能（デバック時のみ表示）
			// echo $e->getMessage();
		}
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
			<title>ログイン</title>
	</head>
	<body>
		<h1>ログイン画面</h1>
		<form id="loginForm" name="loginForm" action="" method="POST">
			<fieldset>  <!-- fieldsetはグループ化してくれる(線で囲ってくれる) -->
				<legend>ログインフォーム</legend>  <!-- グループの先頭には、<LEGEND>～</LEGEND>で入力項目グループにタイトルをつけます。 -->
				<div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
				<label for="userid">ユーザーID</label><input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">  <!-- 初回起動はユーザーID空白にして、２回目以降はPOST送信したユーザーIDが保存されている。 -->
				<br>
				<label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力"> <!-- プレースホルダーは入力欄に薄く文字を表示させるもの -->
				<br>
				<input type="submit" id="login" name="login" value="ログイン">
			</fieldset>
		</form>
		<br>
		<form action="SignUp.php">
			<fieldset>			
				<legend>新規登録フォーム</legend>
				<input type="submit" value="新規登録">
			</fieldset>
		</form>
	</body>
</html>

