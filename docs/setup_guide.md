# XAMPPを使用した環境構築ガイド

## 1. XAMPPのインストールと設定

### 1.1 XAMPPのインストール
1. [XAMPPの公式サイト](https://www.apachefriends.org/)からダウンロード
2. インストーラーを実行（デフォルトの設定で問題ありません）

### 1.2 XAMPPの起動
1. XAMPP Control Panelを起動
2. ApacheとMySQLの「Start」ボタンをクリック
3. 両方のサービスが緑色で表示されることを確認

## 2. データベースの作成

### 2.1 phpMyAdminにアクセス
1. ブラウザで `http://localhost/phpmyadmin` にアクセス
2. 左側の「新規作成」をクリック
3. データベース名を `blog_app` として作成

## 3. Laravelプロジェクトの設定

### 3.1 プロジェクトをXAMPPのhtdocsに移動
1. 現在の`blog-app`フォルダを`C:\xampp\htdocs\`にコピー

### 3.2 作業ディレクトリの確認
- プロジェクトの作業は全て `C:\xampp\htdocs\blog-app` で行います
- コマンドプロンプトやPowerShellで作業する場合は、必ずこのディレクトリに移動してから実行してください：
  ```bash
  cd C:\xampp\htdocs\blog-app
  ```

### 3.3 Cursorでの作業
1. Cursorを一度閉じる
2. `C:\xampp\htdocs\blog-app` フォルダを右クリック
3. 「Cursorで開く」を選択
4. 新しいプロジェクトとして開かれるため、新しいチャットとして開始されます
5. 以前のチャット履歴は、元のプロジェクトディレクトリで開いた時に参照できます

### 3.4 .envファイルの設定
以下の内容で`.env`ファイルを設定します：
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=
```

### 3.5 Laravelの設定
以下のコマンドを順番に実行します：

1. キャッシュのクリア：
```bash
php artisan config:cache
```

2. マイグレーションの実行：
```bash
php artisan migrate
```

## 4. アプリケーションの起動

### 4.1 Laravelの開発サーバーを起動
```bash
php artisan serve
```

### 4.2 ブラウザでアクセス
- `http://localhost:8000` にアクセスして、Laravelのウェルカムページが表示されることを確認

## 5. 追加の設定

### 5.1 ファイルアップロード用のディレクトリの権限設定
```bash
php artisan storage:link
```

### 5.2 アプリケーションキーの生成
```bash
php artisan key:generate
```

## トラブルシューティング

### よくある問題と解決方法

1. **XAMPPのサービスが起動しない場合**
   - ポート80や3306が他のアプリケーションで使用されている可能性があります
   - XAMPP Control Panelで「Config」ボタンをクリックし、ポート設定を確認してください

2. **データベース接続エラー**
   - MySQLサービスが起動していることを確認
   - .envファイルの設定が正しいことを確認
   - データベースが作成されていることを確認

3. **Laravelのエラー**
   - キャッシュをクリアしてみてください：
     ```bash
     php artisan config:clear
     php artisan cache:clear
     ```
   - ストレージディレクトリの権限を確認してください

4. **コマンドが実行できない場合**
   - 正しいディレクトリ（`C:\xampp\htdocs\blog-app`）で作業していることを確認
   - PHPがパスに通っていることを確認

## 参考リンク

- [XAMPP公式サイト](https://www.apachefriends.org/)
- [Laravel公式ドキュメント](https://laravel.com/docs)
- [MySQL公式ドキュメント](https://dev.mysql.com/doc/) 

git remote add origin https://github.com/Yusuke-Shibata23/blog-app.git
git branch -M main
git push -u origin main 


php artisan serve
npm run dev