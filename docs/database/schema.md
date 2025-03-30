# データベーススキーマ

## posts テーブル

投稿の基本情報を管理するテーブルです。

### カラム定義

| カラム名 | 型 | 説明 | 制約 |
|----------|-----|------|------|
| id | bigint | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| title | varchar(255) | 投稿タイトル | NOT NULL |
| content | text | 投稿内容 | NOT NULL |
| status | varchar(20) | 投稿ステータス | NOT NULL, ENUM('draft', 'published', 'scheduled') |
| scheduled_at | datetime | 予約投稿日時 | NULL |
| created_at | timestamp | 作成日時 | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| updated_at | timestamp | 更新日時 | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP |

### インデックス

- PRIMARY KEY (id)
- INDEX idx_status (status)
- INDEX idx_scheduled_at (scheduled_at)

## post_images テーブル

投稿に添付される画像を管理するテーブルです。

### カラム定義

| カラム名 | 型 | 説明 | 制約 |
|----------|-----|------|------|
| id | bigint | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| post_id | bigint | 投稿ID | NOT NULL, FOREIGN KEY |
| image_path | varchar(255) | 画像パス | NOT NULL |
| created_at | timestamp | 作成日時 | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| updated_at | timestamp | 更新日時 | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP |

### インデックス

- PRIMARY KEY (id)
- FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
- INDEX idx_post_id (post_id)

## リレーション

- posts 1:N post_images
  - 1つの投稿に複数の画像を添付可能
  - 投稿が削除された場合、関連する画像も自動的に削除される 