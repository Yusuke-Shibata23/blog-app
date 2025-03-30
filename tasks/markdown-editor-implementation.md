# Markdownエディター実装タスク

## 参照すべきファイル

### フロントエンド
- `resources/js/components/PostForm.vue` - 投稿フォームコンポーネント
- `resources/js/components/MarkdownEditor.vue` - 新規作成するMarkdownエディターコンポーネント
- `resources/js/views/PostCreate.vue` - 投稿作成画面
- `resources/js/views/PostEdit.vue` - 投稿編集画面

### バックエンド
- `app/Http/Controllers/PostController.php` - 投稿のCRUD処理
- `app/Models/Post.php` - 投稿モデル
- `database/migrations/xxxx_xx_xx_create_posts_table.php` - 投稿テーブルのマイグレーション

## 必要な変更内容

### 1. Markdownエディターコンポーネントの作成
- `resources/js/components/MarkdownEditor.vue`の作成
  - エディター部分（textarea）
  - プレビュー部分
  - ツールバー（太字、イタリック、リスト等）
  - 画像アップロード機能

### 2. PostForm.vueの修正
- TinyMCEエディターの削除
- MarkdownEditorコンポーネントの統合
- フォームデータの構造変更
- プレビュー機能の追加

### 3. バックエンドの修正
- `PostController.php`の修正
  - Markdownコンテンツの処理
  - 画像アップロード処理の調整
- バリデーションルールの更新

## 使用すべき言語機能やアプローチ

### フロントエンド
- Vue.js 3 Composition API
- Tailwind CSS
- marked.js（Markdownパース用）
- highlight.js（コードハイライト用）

### バックエンド
- Laravel 10
- PHP 8.1以上
- Intervention Image（画像処理用）

## 制約条件

### 機能面
1. Markdown記法のサポート
   - 見出し（h1-h6）
   - 太字、イタリック
   - リスト（順序付き、順序なし）
   - リンク
   - 画像
   - コードブロック
   - 引用
   - テーブル

2. 画像アップロード
   - 最大5枚まで
   - 対応フォーマット：JPG, PNG, GIF
   - 最大サイズ：10MB
   - ドラッグ＆ドロップ対応

3. プレビュー機能
   - リアルタイムプレビュー
   - コードハイライト
   - 画像プレビュー

### パフォーマンス
1. エディターの応答性
   - 入力遅延を最小限に
   - プレビューの更新は適度な間隔で

2. 画像処理
   - アップロード前の圧縮
   - 適切なサイズへのリサイズ

### セキュリティ
1. XSS対策
   - Markdownのサニタイズ
   - 画像のバリデーション

2. CSRF対策
   - LaravelのCSRFトークン
   - 画像アップロード時の認証

### ブラウザ対応
- モダンブラウザ（Chrome, Firefox, Safari, Edge）
- モバイルブラウザ対応
- レスポンシブデザイン

## 実装手順

1. 環境セットアップ
   - 必要なパッケージのインストール
   - 設定ファイルの作成

2. MarkdownEditorコンポーネントの実装
   - 基本的なエディター機能
   - ツールバーの実装
   - プレビュー機能

3. PostForm.vueの修正
   - TinyMCEの削除
   - MarkdownEditorの統合
   - フォームデータの調整

4. バックエンドの修正
   - コントローラーの更新
   - バリデーションの調整
   - 画像処理の最適化

5. テスト
   - ユニットテスト
   - 統合テスト
   - ブラウザテスト

6. デプロイ
   - 本番環境への適用
   - パフォーマンスモニタリング 