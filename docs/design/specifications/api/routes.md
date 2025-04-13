# APIルート仕様書

## 1. 認証関連のルート（認証不要）

### 1.1 ユーザー登録
- **エンドポイント**: `POST /api/register`
- **リクエストボディ**:
```json
{
    "name": "ユーザー名",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```
- **レスポンス**:
  - 成功時（201）:
```json
{
    "user": {
        "id": 1,
        "name": "ユーザー名",
        "email": "user@example.com",
        "created_at": "2024-03-30T01:56:00.000000Z",
        "updated_at": "2024-03-30T01:56:00.000000Z"
    },
    "token": "アクセストークン"
}
```
  - エラー時（422）: バリデーションエラーメッセージ

### 1.2 ログイン
- **エンドポイント**: `POST /api/login`
- **リクエストボディ**:
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```
- **レスポンス**:
  - 成功時（200）:
```json
{
    "user": {
        "id": 1,
        "name": "ユーザー名",
        "email": "user@example.com",
        "created_at": "2024-03-30T01:56:00.000000Z",
        "updated_at": "2024-03-30T01:56:00.000000Z"
    },
    "token": "アクセストークン"
}
```
  - エラー時（401）: 認証エラーメッセージ

## 2. 認証が必要なルート

### 2.1 ログアウト
- **エンドポイント**: `POST /api/logout`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）:
```json
{
    "message": "ログアウトしました。"
}
```

### 2.2 ユーザー情報取得
- **エンドポイント**: `GET /api/user`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）:
```json
{
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com",
    "created_at": "2024-03-30T01:56:00.000000Z",
    "updated_at": "2024-03-30T01:56:00.000000Z"
}
```

### 2.3 投稿関連

#### 2.3.1 下書き一覧取得
- **エンドポイント**: `GET /api/posts/drafts`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: 下書き投稿の配列

#### 2.3.2 予約投稿一覧取得
- **エンドポイント**: `GET /api/posts/scheduled`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: 予約投稿の配列

#### 2.3.3 公開済み投稿一覧取得
- **エンドポイント**: `GET /api/posts/published`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: 公開済み投稿の配列

#### 2.3.4 自分の投稿一覧取得
- **エンドポイント**: `GET /api/posts/my`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: 自分の投稿の配列

#### 2.3.5 投稿作成
- **エンドポイント**: `POST /api/posts`
- **ヘッダー**: `Authorization: Bearer {token}`
- **リクエストボディ**: 投稿データ
- **レスポンス**:
  - 成功時（201）: 作成された投稿データ

#### 2.3.6 投稿詳細取得
- **エンドポイント**: `GET /api/posts/{post}`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: 投稿詳細データ

#### 2.3.7 投稿更新
- **エンドポイント**: `PUT /api/posts/{post}`
- **ヘッダー**: `Authorization: Bearer {token}`
- **リクエストボディ**: 更新する投稿データ
- **レスポンス**:
  - 成功時（200）: 更新された投稿データ

#### 2.3.8 投稿削除
- **エンドポイント**: `DELETE /api/posts/{post}`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（204）: レスポンスボディなし

### 2.4 画像アップロード
- **エンドポイント**: `POST /api/upload-image`
- **ヘッダー**: `Authorization: Bearer {token}`
- **リクエストボディ**: 画像ファイル
- **レスポンス**:
  - 成功時（200）: アップロードされた画像のURL

### 2.5 いいね機能

#### 2.5.1 いいねのトグル
- **エンドポイント**: `POST /api/posts/{post}/toggle-like`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: いいね状態の更新結果

#### 2.5.2 いいね状態取得
- **エンドポイント**: `GET /api/posts/{post}/like-status`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: いいね状態データ

#### 2.5.3 いいねした投稿一覧取得
- **エンドポイント**: `GET /api/posts/liked`
- **ヘッダー**: `Authorization: Bearer {token}`
- **レスポンス**:
  - 成功時（200）: いいねした投稿の配列

## 3. 公開投稿関連のルート（認証不要）

### 3.1 公開投稿一覧取得
- **エンドポイント**: `GET /api/posts`
- **レスポンス**:
  - 成功時（200）: 公開投稿の配列

### 3.2 公開投稿詳細取得
- **エンドポイント**: `GET /api/posts/public/{post}`
- **レスポンス**:
  - 成功時（200）: 公開投稿の詳細データ 