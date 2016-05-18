# loginManagementを使用するためのデータベース設定

<br>

MySQL管理ツールのphpMyAdminでテーブル作成やデータベース作成などを行う。

## phpMyAdminで行う手順

1. データベース作成
2. ユーザーの作成
3. テーブルの作成

これらを以下の通りに設定する。

### データベース
loginManagement

### ユーザー
- ユーザ：hogeUser
- ホスト：localhost
- パスワード：hogehoge
- グローバル権限：全てにチェック(本来はデータベースに対して必要な権限だけを持つユーザを使うようにする)

###テーブル
userData

- id （int（5）） ... primary key設定、AUTO_INCREMENT（idの被りを無くすため）
- name （varchar（20））
- password （varchar（100）） ... ハッシュ化するから少し大きめ