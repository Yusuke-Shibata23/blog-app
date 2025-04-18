# タグ機能の実装タスク

## 実装方針
- タグは既存に設定されたものから任意に選ぶ（複数選択可能）
- タグは`config/const.php`に定義
- 記事編集時にタグを選択可能
- 選択されたタグは`posts`テーブルの`tags`カラムにJSON形式で保存

## 実装ステップ

### 1. データベース設計
- `posts`テーブルに`tags`カラムを追加
  - 型: JSON
  - デフォルト値: []

### 2. 設定ファイルの準備
- `config/const.php`の作成
- タグの定義リストの追加

### 3. モデルの実装
- `Post`モデルにタグ関連のメソッドを追加
  - タグの取得
  - タグの保存
  - タグの検証

### 4. マイグレーションの作成
- `posts`テーブルに`tags`カラムを追加するマイグレーション

### 5. コントローラーの実装
- タグ選択機能の追加
- タグ保存処理の実装

### 6. ビューの実装
- タグ選択UIの実装
- 選択済みタグの表示

## 注意点
- タグは既存のものからの選択のみとし、新規作成は不可
- 複数選択可能なUIを実装
- タグはJSON形式で保存
- パフォーマンスを考慮した実装

## 完了条件
- 記事編集時にタグを選択できる
- 選択したタグが保存される
- 保存したタグが表示される 