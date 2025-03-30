# 開発ガイド

## 環境構築

### 必要条件
- PHP 8.2以上
- Node.js 18以上
- Composer
- npm または yarn
- MySQL 8.0以上

### 初期セットアップ

1. リポジトリのクローン
```bash
git clone [repository-url]
cd blog-app
```

2. 依存パッケージのインストール
```bash
# PHP依存パッケージのインストール
composer install

# Node.js依存パッケージのインストール
npm install
```

3. 環境設定
```bash
# .envファイルの作成
cp .env.example .env

# アプリケーションキーの生成
php artisan key:generate
```

4. データベースの設定
- `.env`ファイルを編集し、データベース接続情報を設定
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=
```

5. データベースの作成とマイグレーション
```bash
# データベースの作成
php artisan db:create

# マイグレーションの実行
php artisan migrate
```

6. シードデータの投入
```bash
# ユーザーデータの投入
php artisan db:seed --class=UserSeeder

# 投稿データの投入
php artisan db:seed --class=PostSeeder
```

7. ストレージの設定
```bash
# ストレージリンクの作成
php artisan storage:link
```

8. アプリケーションのビルド
```bash
# フロントエンドのビルド
npm run build
```

### アプリケーションの起動

1. 開発サーバーの起動
```bash
# バックエンドサーバーの起動
php artisan serve

# 別のターミナルでフロントエンドの開発サーバーを起動
npm run dev
```

2. アプリケーションへのアクセス
- バックエンドAPI: http://localhost:8000
- フロントエンド: http://localhost:5173

## 開発フロー

### 新しい機能の追加
1. 新しいブランチの作成
```bash
git checkout -b feature/新機能名
```

2. コードの実装
3. テストの作成と実行
```bash
php artisan test
```

4. コードのコミット
```bash
git add .
git commit -m "feat: 新機能の追加"
```

5. プルリクエストの作成

### デプロイ
1. 本番環境の設定
2. ビルドの実行
3. デプロイの実行

## トラブルシューティング

### よくある問題と解決方法

1. データベース接続エラー
- `.env`ファイルの設定を確認
- データベースサーバーが起動しているか確認

2. 依存パッケージのインストールエラー
- Composerのキャッシュをクリア
```bash
composer clear-cache
```
- npmのキャッシュをクリア
```bash
npm cache clean --force
```

3. ストレージの問題
- ストレージリンクが正しく作成されているか確認
- パーミッションの設定を確認

## テスト

### テストの実行
```bash
# 全テストの実行
php artisan test

# 特定のテストの実行
php artisan test --filter=テスト名
```

### テストカバレッジの確認
```bash
php artisan test --coverage-html coverage
```

## コード規約

### PHP
- PSR-12に準拠
- メソッド名はcamelCase
- クラス名はPascalCase

### JavaScript/Vue
- ESLintのルールに準拠
- コンポーネント名はPascalCase
- メソッド名はcamelCase

## デプロイメント

### 本番環境へのデプロイ
1. コードのビルド
```bash
npm run build
```

2. 環境変数の設定
3. データベースのマイグレーション
```bash
php artisan migrate --force
```

4. キャッシュのクリア
```bash
php artisan config:clear
php artisan cache:clear
```

5. アプリケーションの再起動 