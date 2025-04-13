# ユーザー情報取得機能仕様書

## 1. 概要
- **目的**: ログイン中のユーザー情報を取得する
- **主要機能**: 認証済みユーザーの情報取得
- **対象ユーザー**: ログイン中のユーザー

## 2. 機能一覧
| 機能ID | 機能名 | 説明 | 優先度 |
|--------|--------|------|--------|
| USER001 | ユーザー情報取得 | ログインユーザーの情報を取得 | 高 |

## 3. レスポンス形式
```json
{
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com",
    "created_at": "2024-03-30T01:56:00.000000Z",
    "updated_at": "2024-03-30T01:56:00.000000Z"
}
```

## 4. セキュリティ要件
- 認証済みユーザーのみアクセス可能
- トークンの検証（Laravelデフォルト）

## 5. パフォーマンス要件
- レスポンス時間: 1秒以内

## 6. コンポーネント設計

### 6.1 ユーザー情報表示
```vue
<template>
  <div v-if="user" class="user-info">
    <h2>{{ user.name }}</h2>
    <p>{{ user.email }}</p>
  </div>
  <div v-else class="loading">
    読み込み中...
  </div>
</template>
```

### 6.2 状態管理
```typescript
interface UserState {
  user: User | null;
  loading: boolean;
}

const state = reactive<UserState>({
  user: null,
  loading: true
});
```

### 6.3 メソッド
```typescript
const fetchUser = async () => {
  state.loading = true;
  
  try {
    const response = await authStore.getUser();
    state.user = response.data;
  } finally {
    state.loading = false;
  }
};
``` 