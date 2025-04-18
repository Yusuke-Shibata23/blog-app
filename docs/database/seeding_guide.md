# ブログアプリケーション シードデータ作成ガイド

## 概要
このガイドでは、ブログアプリケーションの初期データ投入方法について説明します。

## シードデータの内容

### 1. ユーザーデータ
- 管理者ユーザー（1名）
- 一般ユーザー（3名）

### 2. ブログ投稿データ
- 公開済み投稿（5件）
- 下書き投稿（3件）
- 予約投稿（2件）

### 3. タグデータ
- 技術系タグ（5個）
- ライフスタイル系タグ（3個）
- その他（2個）

## シードデータの実行方法

### 1. データベースのリセット
```bash
php artisan migrate:fresh
```

### 2. シードデータの投入
```bash
php artisan db:seed
```

### 3. 特定のシーダーのみ実行
```bash
# ユーザーデータのみ
php artisan db:seed --class=UserSeeder

# 投稿データのみ
php artisan db:seed --class=PostSeeder
```

## シードデータの構造

### UserSeeder
```php
[
    'name' => '管理者',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
],
[
    'name' => '一般ユーザー1',
    'email' => 'user1@example.com',
    'password' => Hash::make('password'),
    'role' => 'user'
]
```

### PostSeeder
```php
[
    'title' => 'サンプル投稿タイトル',
    'content' => 'サンプル投稿内容...',
    'status' => 'published',
    'published_at' => now(),
    'tags' => ['技術', 'プログラミング'],
    'user_id' => 1
]
```

## 注意事項
1. シードデータを実行する前に、必ずデータベースのバックアップを取得してください
2. 本番環境での実行は避けてください
3. シードデータは開発・テスト目的のみで使用してください

## トラブルシューティング
1. シードデータの実行に失敗した場合
   - データベースの接続設定を確認
   - マイグレーションが正常に実行されているか確認
   - エラーメッセージを確認して対応

2. データが正しく投入されない場合
   - シーダーファイルの内容を確認
   - 外部キー制約に問題がないか確認
   - データベースの状態を確認 