# APIエンドポイント設計書

## 1. 認証API

### 1.1 ユーザー登録
- **エンドポイント**: `POST /api/register`
- **説明**: 新規ユーザーの登録
- **リクエスト**:
```json
{
    "name": "string",                // 必須、最大255文字
    "email": "string",               // 必須、メール形式、最大255文字、重複不可
    "password": "string",            // 必須、最小8文字
    "password_confirmation": "string" // 必須、passwordと一致
}
```
- **レスポンス**:
```json
{
    "user": {
        "id": "integer",
        "name": "string",
        "email": "string",
        "created_at": "datetime",
        "updated_at": "datetime"
    },
    "token": "string"  // Bearerトークン
}
```
- **ステータスコード**: 201 Created
- **エラーレスポンス**:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": ["エラーメッセージ"]
    }
}
```

### 1.2 ログイン
- **エンドポイント**: `POST /api/login`
- **説明**: ユーザーの認証を行い、アクセストークンを発行
- **リクエスト**:
```json
{
    "email": "string",    // 必須、メール形式
    "password": "string"  // 必須
}
```
- **レスポンス**:
```json
{
    "user": {
        "id": "integer",
        "name": "string",
        "email": "string",
        "created_at": "datetime",
        "updated_at": "datetime"
    },
    "token": "string"  // Bearerトークン
}
```
- **ステータスコード**: 200 OK
- **エラーレスポンス**:
```json
{
    "message": "認証情報が正しくありません。",
    "errors": {
        "email": ["認証情報が正しくありません。"]
    }
}
```

### 1.3 ログアウト
- **エンドポイント**: `POST /api/logout`
- **説明**: ユーザーのログアウト処理
- **リクエスト**: なし
- **レスポンス**:
```json
{
    "message": "ログアウトしました。"
}
```
- **ステータスコード**: 200 OK
- **認証**: 必須（Bearerトークン）

### 1.4 ログインユーザー情報取得
- **エンドポイント**: `GET /api/user`
- **説明**: ログインユーザーの情報を取得
- **リクエスト**: なし
- **レスポンス**:
```json
{
    "id": "integer",
    "name": "string",
    "email": "string",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```
- **ステータスコード**: 200 OK
- **認証**: 必須（Bearerトークン）

## 2. ユーザーAPI

### 2.1 ユーザー情報取得
- **エンドポイント**: `GET /api/users/{id}`
- **説明**: 指定したIDのユーザー情報を取得
- **リクエスト**: なし
- **レスポンス**:
```json
{
    "status": "success",
    "data": {
        "user": {
            "id": "integer",
            "name": "string",
            "email": "string",
            "created_at": "datetime",
            "updated_at": "datetime"
        }
    }
}
```

## 3. 投稿API

### 3.1 投稿一覧取得
- **エンドポイント**: `GET /api/posts`
- **説明**: 投稿の一覧を取得
- **クエリパラメータ**:
  - `page`: ページ番号
  - `per_page`: 1ページあたりの件数
  - `tag`: タグによるフィルタリング
- **レスポンス**:
```json
{
    "status": "success",
    "data": {
        "posts": [
            {
                "id": "integer",
                "title": "string",
                "content": "string",
                "user_id": "integer",
                "created_at": "datetime",
                "updated_at": "datetime",
                "tags": ["string"]
            }
        ],
        "meta": {
            "current_page": "integer",
            "total": "integer",
            "per_page": "integer"
        }
    }
}
```

### 3.2 投稿作成
- **エンドポイント**: `POST /api/posts`
- **説明**: 新規投稿を作成
- **リクエスト**:
```json
{
    "title": "string",
    "content": "string",
    "tags": ["string"]
}
```
- **レスポンス**:
```json
{
    "status": "success",
    "data": {
        "post": {
            "id": "integer",
            "title": "string",
            "content": "string",
            "user_id": "integer",
            "created_at": "datetime",
            "updated_at": "datetime",
            "tags": ["string"]
        }
    }
}
```

## 4. 共通仕様

### 4.1 認証方式
- Bearer Token認証
- トークンはリクエストヘッダーに設定
```
Authorization: Bearer {token}
```

### 4.2 エラーレスポンス
- バリデーションエラー: 422 Unprocessable Entity
- 認証エラー: 401 Unauthorized
- 権限エラー: 403 Forbidden

### 4.3 リクエストヘッダー
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  // 認証が必要なエンドポイントの場合
```

### 4.4 レスポンスヘッダー
```
Content-Type: application/json
```

### 4.5 レート制限
- 1分間に60リクエストまで
- 制限超過時は429ステータスコードを返却 