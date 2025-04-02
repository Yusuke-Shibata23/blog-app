# APIエンドポイント

## 認証

### ログイン
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "string",
    "password": "string"
}
```

**レスポンス**
```json
{
    "access_token": "string",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### ログアウト
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### ユーザー登録
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "string",
    "email": "string",
    "password": "string",
    "password_confirmation": "string"
}
```

## ユーザー

### ユーザー情報の取得
```http
GET /api/user
Authorization: Bearer {token}
```

### ユーザー情報の更新
```http
PUT /api/user
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "string",
    "email": "string"
}
```

## 投稿

### 投稿一覧の取得
```http
GET /api/posts
```

**クエリパラメータ**
- page: ページ番号
- per_page: 1ページあたりの件数
- category: カテゴリID
- tag: タグID
- search: 検索キーワード

### 投稿の作成
```http
POST /api/posts
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "string",
    "content": "string",
    "category_id": "integer",
    "tags": ["integer"],
    "published": "boolean"
}
```

### 投稿の取得
```http
GET /api/posts/{id}
```

### 投稿の更新
```http
PUT /api/posts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "string",
    "content": "string",
    "category_id": "integer",
    "tags": ["integer"],
    "published": "boolean"
}
```

### 投稿の削除
```http
DELETE /api/posts/{id}
Authorization: Bearer {token}
```

## コメント

### コメント一覧の取得
```http
GET /api/posts/{postId}/comments
```

### コメントの作成
```http
POST /api/posts/{postId}/comments
Authorization: Bearer {token}
Content-Type: application/json

{
    "content": "string"
}
```

### コメントの更新
```http
PUT /api/comments/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "content": "string"
}
```

### コメントの削除
```http
DELETE /api/comments/{id}
Authorization: Bearer {token}
```

## カテゴリ

### カテゴリ一覧の取得
```http
GET /api/categories
```

### カテゴリの作成
```http
POST /api/categories
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "string",
    "parent_id": "integer"
}
```

### カテゴリの更新
```http
PUT /api/categories/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "string",
    "parent_id": "integer"
}
```

### カテゴリの削除
```http
DELETE /api/categories/{id}
Authorization: Bearer {token}
```

## タグ

### タグ一覧の取得
```http
GET /api/tags
```

### タグの作成
```http
POST /api/tags
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "string"
}
```

### タグの更新
```http
PUT /api/tags/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "string"
}
```

### タグの削除
```http
DELETE /api/tags/{id}
Authorization: Bearer {token}
```

## メディア

### メディアのアップロード
```http
POST /api/media
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "file": "file",
    "post_id": "integer"
}
```

### メディアの削除
```http
DELETE /api/media/{id}
Authorization: Bearer {token}
```

## エラーレスポンス

### 400 Bad Request
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field": ["エラーメッセージ"]
    }
}
```

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
    "message": "Not Found"
}
```

### 500 Internal Server Error
```json
{
    "message": "Server Error"
}
``` 