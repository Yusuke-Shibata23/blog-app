# API エンドポイント

## 投稿関連

### 一覧取得
```
GET /api/posts
```

#### クエリパラメータ
| パラメータ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| status | string | ステータスでフィルタリング | 任意 |
| search | string | 検索キーワード | 任意 |
| page | integer | ページ番号 | 任意 |
| per_page | integer | 1ページあたりの件数 | 任意 |

#### レスポンス
```json
{
  "data": [
    {
      "id": 1,
      "title": "投稿タイトル",
      "content": "投稿内容",
      "status": "published",
      "scheduled_at": null,
      "created_at": "2024-03-20T10:00:00.000000Z",
      "updated_at": "2024-03-20T10:00:00.000000Z",
      "images": [
        {
          "id": 1,
          "image_path": "path/to/image.jpg"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### 詳細取得
```
GET /api/posts/{id}
```

#### パスパラメータ
| パラメータ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| id | integer | 投稿ID | 必須 |

#### レスポンス
```json
{
  "id": 1,
  "title": "投稿タイトル",
  "content": "投稿内容",
  "status": "published",
  "scheduled_at": null,
  "created_at": "2024-03-20T10:00:00.000000Z",
  "updated_at": "2024-03-20T10:00:00.000000Z",
  "images": [
    {
      "id": 1,
      "image_path": "path/to/image.jpg"
    }
  ]
}
```

### 作成
```
POST /api/posts
```

#### リクエストボディ
| パラメータ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| title | string | 投稿タイトル | 必須 |
| content | string | 投稿内容 | 必須 |
| status | string | 投稿ステータス | 必須 |
| scheduled_at | datetime | 予約投稿日時 | 任意 |
| images[] | file | 画像ファイル | 任意 |

#### レスポンス
```json
{
  "message": "投稿が作成されました",
  "post": {
    "id": 1,
    "title": "投稿タイトル",
    "content": "投稿内容",
    "status": "published",
    "scheduled_at": null,
    "created_at": "2024-03-20T10:00:00.000000Z",
    "updated_at": "2024-03-20T10:00:00.000000Z",
    "images": [
      {
        "id": 1,
        "image_path": "path/to/image.jpg"
      }
    ]
  }
}
```

### 更新
```
PUT /api/posts/{id}
```

#### パスパラメータ
| パラメータ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| id | integer | 投稿ID | 必須 |

#### リクエストボディ
| パラメータ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| title | string | 投稿タイトル | 必須 |
| content | string | 投稿内容 | 必須 |
| status | string | 投稿ステータス | 必須 |
| scheduled_at | datetime | 予約投稿日時 | 任意 |
| images[] | file | 新規画像ファイル | 任意 |
| existing_images[] | string | 既存画像のパス | 任意 |
| deleted_images[] | integer | 削除対象の画像ID | 任意 |

#### レスポンス
```json
{
  "message": "投稿が更新されました",
  "post": {
    "id": 1,
    "title": "投稿タイトル",
    "content": "投稿内容",
    "status": "published",
    "scheduled_at": null,
    "created_at": "2024-03-20T10:00:00.000000Z",
    "updated_at": "2024-03-20T10:00:00.000000Z",
    "images": [
      {
        "id": 1,
        "image_path": "path/to/image.jpg"
      }
    ]
  }
}
```

### 削除
```
DELETE /api/posts/{id}
```

#### パスパラメータ
| パラメータ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| id | integer | 投稿ID | 必須 |

#### レスポンス
```json
{
  "message": "投稿が削除されました"
}
``` 