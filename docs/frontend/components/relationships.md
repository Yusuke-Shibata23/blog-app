# コンポーネント間の関係性

## レイアウト階層

```mermaid
graph TD
    A[AppLayout] --> B[Header]
    A --> C[MainContent]
    A --> D[Footer]
    A --> E[Sidebar]
    
    F[AuthLayout] --> G[AuthHeader]
    F --> H[AuthCard]
    F --> I[AuthFooter]
    
    J[AdminLayout] --> K[AdminHeader]
    J --> L[AdminSidebar]
    J --> M[AdminContent]
    J --> N[Breadcrumb]
```

## コンポーネントの依存関係

### レイアウトコンポーネント
- `AppLayout`は以下のコンポーネントに依存：
  - `Header` - ナビゲーションとユーザー情報
  - `Sidebar` - メニューナビゲーション
  - `Footer` - フッター情報

- `AuthLayout`は以下のコンポーネントに依存：
  - `AuthHeader` - ロゴとタイトル
  - `AuthCard` - 認証フォームのコンテナ
  - `AuthFooter` - 認証関連リンク

- `AdminLayout`は以下のコンポーネントに依存：
  - `AdminHeader` - 管理画面ヘッダー
  - `AdminSidebar` - 管理メニュー
  - `Breadcrumb` - ナビゲーション階層

### 共通コンポーネント
- `Button`は以下のコンポーネントで使用：
  - `Header` - ログインボタン
  - `AuthCard` - フォーム送信ボタン
  - `Modal` - アクションボタン

- `Input`は以下のコンポーネントで使用：
  - `AuthCard` - ログインフォーム
  - `PostForm` - 投稿フォーム
  - `CommentForm` - コメントフォーム

- `Card`は以下のコンポーネントで使用：
  - `PostList` - 投稿カード
  - `UserProfile` - プロフィールカード
  - `CategoryList` - カテゴリーカード

## データフロー

### 認証フロー
```mermaid
sequenceDiagram
    participant U as User
    participant A as AuthLayout
    participant L as LoginForm
    participant S as AuthStore
    participant API as Backend API

    U->>A: ログインページ表示
    A->>L: フォームレンダリング
    U->>L: 認証情報入力
    L->>S: 認証リクエスト
    S->>API: 認証API呼び出し
    API-->>S: トークン返却
    S-->>L: 認証成功
    L-->>A: リダイレクト
```

### 投稿フロー
```mermaid
sequenceDiagram
    participant U as User
    participant P as PostList
    participant F as PostForm
    participant S as PostStore
    participant API as Backend API

    U->>P: 投稿一覧表示
    P->>S: 投稿取得リクエスト
    S->>API: 投稿API呼び出し
    API-->>S: 投稿データ返却
    S-->>P: 投稿一覧表示

    U->>F: 新規投稿作成
    F->>S: 投稿保存リクエスト
    S->>API: 投稿API呼び出し
    API-->>S: 投稿データ返却
    S-->>F: 保存完了
    F-->>P: 一覧更新
```

### コメントフロー
```mermaid
sequenceDiagram
    participant U as User
    participant P as PostDetail
    participant C as CommentForm
    participant S as CommentStore
    participant API as Backend API

    U->>P: 投稿詳細表示
    P->>S: コメント取得リクエスト
    S->>API: コメントAPI呼び出し
    API-->>S: コメントデータ返却
    S-->>P: コメント表示

    U->>C: コメント入力
    C->>S: コメント保存リクエスト
    S->>API: コメントAPI呼び出し
    API-->>S: コメントデータ返却
    S-->>C: 保存完了
    C-->>P: コメント更新
```

## 状態管理

### Pinia Storeの関係性
```mermaid
graph TD
    A[RootStore] --> B[AuthStore]
    A --> C[PostStore]
    A --> D[CommentStore]
    A --> E[UserStore]
    
    C --> F[CategoryStore]
    C --> G[TagStore]
    
    B --> H[TokenStore]
```

### Storeの依存関係
- `AuthStore`は`TokenStore`に依存
- `PostStore`は`CategoryStore`と`TagStore`に依存
- `CommentStore`は`PostStore`と`UserStore`に依存

## コンポーネントの再利用性

### 共通コンポーネント
- `Button` - 全ページで使用可能
- `Input` - フォーム全般で使用可能
- `Card` - コンテンツ表示全般で使用可能
- `Modal` - ポップアップ表示全般で使用可能
- `Pagination` - リスト表示全般で使用可能
- `Loading` - ローディング表示全般で使用可能

### レイアウトコンポーネント
- `AppLayout` - 一般ページ用
- `AuthLayout` - 認証関連ページ用
- `AdminLayout` - 管理画面用

## パフォーマンス最適化

### コンポーネントの遅延読み込み
```typescript
// ルーティング設定
const routes = [
    {
        path: '/admin',
        component: () => import('@/layouts/AdminLayout.vue'),
        children: [
            {
                path: 'posts',
                component: () => import('@/views/admin/PostList.vue')
            }
        ]
    }
];
```

### コンポーネントのメモ化
```typescript
// メモ化されたコンポーネント
const MemoizedPostCard = defineComponent({
    name: 'MemoizedPostCard',
    props: {
        post: {
            type: Object as PropType<Post>,
            required: true
        }
    },
    setup(props) {
        return () => h(PostCard, { post: props.post });
    }
});
``` 