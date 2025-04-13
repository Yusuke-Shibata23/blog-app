# ログイン画面仕様書

## 1. 概要
- **目的**: ユーザーの認証を行う
- **主要機能**: メールアドレスとパスワードによる認証
- **対象ユーザー**: 登録済みユーザー

## 2. 画面レイアウト
```mermaid
graph TD
    A[ヘッダー] --> B[ログインフォーム]
    B --> C[メールアドレス入力]
    B --> D[パスワード入力]
    B --> E[ログインボタン]
    B --> F[新規登録リンク]
    B --> G[エラーメッセージ]
```

## 3. 画面遷移
```mermaid
graph LR
    A[トップページ] --> B[ログイン画面]
    B --> C[新規登録画面]
    B --> D[ダッシュボード]
```

## 4. 機能一覧
| 機能ID | 機能名 | 説明 | 優先度 |
|--------|--------|------|--------|
| AUTH001 | ログイン認証 | メールアドレスとパスワードによる認証 | 高 |
| AUTH002 | 新規登録遷移 | 新規登録画面への遷移 | 中 |
| AUTH003 | エラー表示 | 認証エラーの表示 | 高 |

## 5. 入力項目
| 項目ID | 項目名 | 型 | 必須 | バリデーション | 備考 |
|--------|--------|----|------|--------------|------|
| AUTH001 | メールアドレス | email | 必須 | メール形式 | - |
| AUTH002 | パスワード | password | 必須 | - | - |

## 6. エラーメッセージ
| エラーコード | メッセージ | 発生条件 |
|-------------|-----------|----------|
| AUTH001 | 認証情報が正しくありません。 | 認証失敗 |
| AUTH002 | メールアドレスを入力してください | メールアドレス未入力 |
| AUTH003 | パスワードを入力してください | パスワード未入力 |

## 7. アクセシビリティ要件
- キーボード操作で全ての機能が利用可能
- スクリーンリーダー対応
- 色のコントラスト比4.5:1以上
- エラーメッセージは視覚的かつ音声で通知
- フォームの各項目に適切なラベルと説明文を付与

## 8. セキュリティ要件
- CSRFトークンの検証（Laravelデフォルト）
- パスワード入力欄はマスク表示
- パスワードの暗号化（bcrypt）
- セキュアなCookie設定（Laravelデフォルト）

## 9. パフォーマンス要件
- ページロード時間: 2秒以内
- フォーム送信後のレスポンス時間: 1秒以内
- エラーメッセージの表示時間: 0.5秒以内

## 10. コンポーネント設計

### 10.1 ログインフォーム
```vue
<template>
  <form @submit.prevent="handleLogin">
    <div class="form-group">
      <label for="email">メールアドレス</label>
      <input
        id="email"
        v-model="email"
        type="email"
        required
        :disabled="loading"
      >
    </div>
    <div class="form-group">
      <label for="password">パスワード</label>
      <input
        id="password"
        v-model="password"
        type="password"
        required
        :disabled="loading"
      >
    </div>
    <button
      type="submit"
      :disabled="loading"
    >
      {{ loading ? 'ログイン中...' : 'ログイン' }}
    </button>
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
  </form>
</template>
```

### 10.2 状態管理
```typescript
interface LoginState {
  email: string;
  password: string;
  loading: boolean;
  error: string | null;
}

const state = reactive<LoginState>({
  email: '',
  password: '',
  loading: false,
  error: null
});
```

### 10.3 メソッド
```typescript
const handleLogin = async () => {
  state.loading = true;
  state.error = null;
  
  try {
    await authStore.login({
      email: state.email,
      password: state.password
    });
    router.push('/dashboard');
  } catch (error) {
    state.error = '認証情報が正しくありません。';
  } finally {
    state.loading = false;
  }
};
``` 