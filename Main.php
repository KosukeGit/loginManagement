<?php
session_start();

// ログイン状態チェック
if (!isset($_SESSION["NAME"])) {
	header("Location: Logout.php");
	exit;
}

?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>メイン</title>
	</head>
	<body>
		<h1>メイン画面</h1>
		<!-- ユーザーIDにHTMLタグが含まれても良いようにエスケープする -->
		<p>ようこそ<u><?php echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES); ?></u>さん</p>  <!-- ユーザー名をechoで表示 --> <!-- uタグはアンダーバー -->
		<ul>  <!-- ・をのついたものをひとかたまりにまとめる -->
			<li><a href="Logout.php">ログアウト</a></li>  <!-- ・をつける -->
		</ul>
	</body>
</html>
