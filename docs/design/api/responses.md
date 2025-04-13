# APIレスポンス形式設計書

## 1. 共通レスポンス形式

### 1.1 成功レスポンス
```json
{
    "status": "success",
    "data": {
        // データオブジェクト
    },
    "meta": {
        // メタデータ（オプション）
    }
}
```

### 1.2 エラーレスポンス
```json
{
    "status": "error",
    "message": "エラーメッセージ",
    "errors": {
        // エラー詳細（オプション）
    }
}
```

## 2. ステータスコード

### 2.1 成功系
- `200 OK`: リクエスト成功
- `201 Created`: リソース作成成功
- `204 No Content`: リクエスト成功（レスポンスボディなし）

### 2.2 クライアントエラー
- `400 Bad Request`: リクエストが不正
- `401 Unauthorized`: 認証が必要
- `403 Forbidden`: アクセス権限なし
- `404 Not Found`: リソースが見つからない
- `422 Unprocessable Entity`: バリデーションエラー

### 2.3 サーバーエラー
- `500 Internal Server Error`: サーバー内部エラー
- `503 Service Unavailable`: サービス利用不可

## 3. データ型定義

### 3.1 基本型
```typescript
type BasicType = {
    string: string;      // 文字列
    integer: number;     // 整数
    float: number;       // 浮動小数点数
    boolean: boolean;    // 真偽値
    datetime: string;    // ISO 8601形式の日時
    date: string;        // ISO 8601形式の日付
    time: string;        // ISO 8601形式の時刻
}
```

### 3.2 複合型
```typescript
type ComplexType = {
    array: T[];         // 配列
    object: {           // オブジェクト
        [key: string]: T;
    };
    nullable: T | null; // null許容
}
```

## 4. リソース別レスポンス形式

### 4.1 ユーザー
```json
{
    "status": "success",
    "data": {
        "user": {
            "id": "integer",
            "name": "string",
            "email": "string",
            "created_at": "datetime",
            "updated_at": "datetime",
            "profile": {
                "bio": "string",
                "avatar_url": "string"
            }
        }
    }
}
```

### 4.2 投稿
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
            "tags": ["string"],
            "author": {
                "id": "integer",
                "name": "string"
            }
        }
    }
}
```

### 4.3 タグ
```json
{
    "status": "success",
    "data": {
        "tag": {
            "id": "integer",
            "name": "string",
            "slug": "string",
            "post_count": "integer"
        }
    }
}
```

## 5. ページネーション

### 5.1 ページネーション付きレスポンス
```json
{
    "status": "success",
    "data": {
        "items": [
            // アイテムの配列
        ],
        "meta": {
            "current_page": "integer",
            "from": "integer",
            "last_page": "integer",
            "per_page": "integer",
            "to": "integer",
            "total": "integer"
        },
        "links": {
            "first": "string",
            "last": "string",
            "prev": "string | null",
            "next": "string | null"
        }
    }
}
```

## 6. エラーレスポンス詳細

### 6.1 バリデーションエラー
```json
{
    "status": "error",
    "message": "The given data was invalid.",
    "errors": {
        "field_name": [
            "エラーメッセージ1",
            "エラーメッセージ2"
        ]
    }
}
```

### 6.2 認証エラー
```json
{
    "status": "error",
    "message": "Unauthenticated.",
    "errors": {
        "auth": ["認証に失敗しました"]
    }
}
```

### 6.3 権限エラー
```json
{
    "status": "error",
    "message": "This action is unauthorized.",
    "errors": {
        "permission": ["この操作を実行する権限がありません"]
    }
}
```

## 7. レスポンスヘッダー

### 7.1 共通ヘッダー
```
Content-Type: application/json
X-Request-ID: {uuid}
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

### 7.2 キャッシュ制御
```
Cache-Control: no-cache, private
Etag: {etag}
Last-Modified: {datetime}
``` 