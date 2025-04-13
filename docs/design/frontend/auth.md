# フロントエンド認証設計書

## 1. 認証状態管理（Pinia Store）

### 1.1 状態（State）
```typescript
interface AuthState {
    user: User | null;          // ユーザー情報
    isAuthenticated: boolean;   // 認証状態
    loading: boolean;          // ローディング状態
}

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}
```

### 1.2 ゲッター（Getters）
```typescript
interface AuthGetters {
    currentUser: User | null;  // 現在のユーザー情報
    isLoggedIn: boolean;       // ログイン状態
}
```

### 1.3 アクション（Actions）

#### 1.3.1 ログイン
```typescript
async login(credentials: {
    email: string;
    password: string;
}): Promise<{
    user: User;
    token: string;
}>
```
- **処理フロー**:
  1. ローディング状態を`true`に設定
  2. `/api/login`にPOSTリクエスト
  3. レスポンスからトークンを取得
  4. トークンを`localStorage`に保存
  5. Axiosのデフォルトヘッダーにトークンを設定
  6. 認証状態を確認
  7. ローディング状態を`false`に設定

#### 1.3.2 登録
```typescript
async register(userData: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}): Promise<{
    user: User;
    token: string;
}>
```
- **処理フロー**:
  1. ローディング状態を`true`に設定
  2. `/api/register`にPOSTリクエスト
  3. レスポンスからトークンを取得
  4. トークンを`localStorage`に保存
  5. Axiosのデフォルトヘッダーにトークンを設定
  6. 認証状態を確認
  7. ローディング状態を`false`に設定

#### 1.3.3 ログアウト
```typescript
async logout(): Promise<void>
```
- **処理フロー**:
  1. ローディング状態を`true`に設定
  2. `/api/logout`にPOSTリクエスト
  3. ユーザー情報を`null`に設定
  4. 認証状態を`false`に設定
  5. `localStorage`からトークンを削除
  6. Axiosのデフォルトヘッダーからトークンを削除
  7. ローディング状態を`false`に設定

#### 1.3.4 認証状態確認
```typescript
async checkAuth(): Promise<void>
```
- **処理フロー**:
  1. `localStorage`からトークンを取得
  2. トークンが存在しない場合:
     - ユーザー情報を`null`に設定
     - 認証状態を`false`に設定
     - 処理を終了
  3. `/api/user`にGETリクエスト
  4. レスポンスが正常な場合:
     - ユーザー情報を更新
     - 認証状態を`true`に設定
  5. エラーの場合:
     - ユーザー情報を`null`に設定
     - 認証状態を`false`に設定

## 2. トークン管理

### 2.1 保存方法
- `localStorage`に保存
- キー: `token`
- 値: Bearerトークン

### 2.2 使用方法
- Axiosのデフォルトヘッダーに設定
```typescript
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
```

### 2.3 削除方法
```typescript
localStorage.removeItem('token');
delete axios.defaults.headers.common['Authorization'];
```

## 3. エラーハンドリング

### 3.1 ログインエラー
```typescript
try {
    await authStore.login(credentials);
} catch (error) {
    console.error('ログインエラー:', error);
    // エラーメッセージを表示
}
```

### 3.2 登録エラー
```typescript
try {
    await authStore.register(userData);
} catch (error) {
    console.error('登録エラー:', error);
    // エラーメッセージを表示
}
```

### 3.3 ログアウトエラー
```typescript
try {
    await authStore.logout();
} catch (error) {
    console.error('ログアウトエラー:', error);
    // エラーメッセージを表示
}
```

## 4. 認証ガード

### 4.1 ルートガード
```typescript
router.beforeEach(async (to, from, next) => {
    if (to.meta.requiresAuth && !authStore.isLoggedIn) {
        next('/login');
    } else {
        next();
    }
});
```

### 4.2 コンポーネントガード
```typescript
onMounted(async () => {
    if (!authStore.isLoggedIn) {
        router.push('/login');
    }
});
``` 