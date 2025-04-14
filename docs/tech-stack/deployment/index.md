# デプロイメント技術スタック

## XAMPP

### 概要
- 統合開発環境
- Windows向けのWebサーバーソフトウェアパッケージ
- ローカル開発環境として使用

### コンポーネント
- Apache
  - Webサーバー
  - モジュール
  - 設定
- MySQL
  - データベースサーバー
  - phpMyAdmin
  - ユーザー管理
- PHP
  - インタプリタ
  - 拡張モジュール
  - 設定

## Git

### 概要
- 分散型バージョン管理システム
- ブランチ管理
- 変更履歴の追跡

### ワークフロー
- ブランチ戦略
  - main
  - develop
  - feature
  - hotfix
- コミット規約
  - コミットメッセージ
  - 変更タイプ
  - スコープ
- マージ戦略
  - マージコミット
  - リベース
  - スカッシュ

## 開発環境

### 必要条件
- PHP 8.2以上
- Node.js 18以上
- Composer
- npm/yarn
- MySQL 8.0以上

### セットアップ
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

## アプリケーションの起動

### 開発サーバー
```bash
# バックエンドサーバーの起動
php artisan serve

# 別のターミナルでフロントエンドの開発サーバーを起動
npm run dev
```

### アクセス
- バックエンドAPI: http://localhost:8000
- フロントエンド: http://localhost:5173 
