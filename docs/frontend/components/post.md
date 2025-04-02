# 投稿関連コンポーネント

## PostList.vue

### 概要
投稿一覧を表示するコンポーネント。ページネーション、フィルタリング、ソート機能を提供。

### プロパティ
```typescript
interface Props {
    initialPosts?: Post[];      // 初期投稿データ
    pagination?: Pagination;    // ページネーション情報
    filters?: PostFilters;      // フィルター条件
}
```

### イベント
```typescript
interface Emits {
    (e: 'update:filters', filters: PostFilters): void;
    (e: 'load-more'): void;
}
```

### データフロー
```mermaid
sequenceDiagram
    participant User
    participant PostList
    participant PostStore
    participant API
    
    User->>PostList: ページ読み込み
    PostList->>PostStore: fetchPosts()
    PostStore->>API: GET /api/posts
    API-->>PostStore: 投稿データ
    PostStore-->>PostList: 状態更新
    PostList-->>User: 投稿一覧表示
    
    User->>PostList: フィルター変更
    PostList->>PostStore: updateFilters()
    PostStore->>API: GET /api/posts?filter=...
    API-->>PostStore: フィルタリングされたデータ
    PostStore-->>PostList: 状態更新
    PostList-->>User: 更新された投稿一覧表示
```

## PostForm.vue

### 概要
投稿の作成・編集フォームコンポーネント。マークダウンエディタ、画像アップロード、タグ選択機能を提供。

### プロパティ
```typescript
interface Props {
    post?: Post;               // 編集対象の投稿データ
    mode: 'create' | 'edit';   // 作成/編集モード
}
```

### イベント
```typescript
interface Emits {
    (e: 'submit', postData: PostCreateData | PostUpdateData): void;
    (e: 'cancel'): void;
}
```

### データフロー
```mermaid
sequenceDiagram
    participant User
    participant PostForm
    participant PostStore
    participant API
    
    User->>PostForm: フォーム入力
    PostForm->>PostStore: validateForm()
    PostStore-->>PostForm: バリデーション結果
    
    alt バリデーション成功
        PostForm->>PostStore: createPost()/updatePost()
        PostStore->>API: POST/PUT /api/posts
        API-->>PostStore: 保存された投稿データ
        PostStore-->>PostForm: 成功通知
        PostForm-->>User: 完了メッセージ表示
    else バリデーション失敗
        PostForm-->>User: エラーメッセージ表示
    end
```

## PostDetail.vue

### 概要
投稿の詳細を表示するコンポーネント。コメント表示、いいね機能、シェア機能を提供。

### プロパティ
```typescript
interface Props {
    postId: number;            // 投稿ID
}
```

### イベント
```typescript
interface Emits {
    (e: 'comment-added', comment: Comment): void;
    (e: 'like-toggled', liked: boolean): void;
}
```

### データフロー
```mermaid
sequenceDiagram
    participant User
    participant PostDetail
    participant PostStore
    participant CommentStore
    participant API
    
    User->>PostDetail: ページ読み込み
    PostDetail->>PostStore: fetchPost(postId)
    PostStore->>API: GET /api/posts/{id}
    API-->>PostStore: 投稿データ
    PostStore-->>PostDetail: 状態更新
    
    PostDetail->>CommentStore: fetchComments(postId)
    CommentStore->>API: GET /api/posts/{id}/comments
    API-->>CommentStore: コメントデータ
    CommentStore-->>PostDetail: 状態更新
    PostDetail-->>User: 投稿詳細とコメント表示
    
    User->>PostDetail: コメント投稿
    PostDetail->>CommentStore: addComment()
    CommentStore->>API: POST /api/posts/{id}/comments
    API-->>CommentStore: 保存されたコメント
    CommentStore-->>PostDetail: 状態更新
    PostDetail-->>User: コメント表示更新
```

## コンポーネント間の連携

### 投稿一覧から詳細への遷移
```mermaid
sequenceDiagram
    participant User
    participant PostList
    participant Router
    participant PostDetail
    
    User->>PostList: 投稿をクリック
    PostList->>Router: router.push('/posts/{id}')
    Router->>PostDetail: コンポーネントマウント
    PostDetail-->>User: 投稿詳細表示
```

### 投稿作成フロー
```mermaid
sequenceDiagram
    participant User
    participant PostList
    participant Router
    participant PostForm
    
    User->>PostList: 「新規投稿」ボタンクリック
    PostList->>Router: router.push('/admin/posts/create')
    Router->>PostForm: コンポーネントマウント
    User->>PostForm: フォーム入力
    PostForm->>PostStore: createPost()
    PostStore-->>PostForm: 成功通知
    PostForm->>Router: router.push('/posts/{id}')
    Router-->>User: 投稿詳細表示
```

## エラーハンドリング

### 投稿取得エラー
```typescript
const handlePostFetchError = (error: any) => {
    if (error.response?.status === 404) {
        showError('投稿が見つかりません');
        router.push('/');
    } else {
        showError('投稿の取得に失敗しました');
    }
};
```

### 投稿保存エラー
```typescript
const handlePostSaveError = (error: any) => {
    if (error.response?.status === 422) {
        // バリデーションエラー
        const errors = error.response.data.errors;
        Object.keys(errors).forEach(key => {
            formErrors.value[key] = errors[key][0];
        });
    } else {
        showError('投稿の保存に失敗しました');
    }
};
```

## パフォーマンス最適化

### 投稿一覧の仮想スクロール
```typescript
const virtualList = {
    items: computed(() => posts.value),
    itemHeight: 200,
    buffer: 5,
    renderItem: (item: Post) => h(PostCard, { post: item })
};
```

### 画像の遅延読み込み
```typescript
const lazyLoadImage = (el: HTMLImageElement) => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                el.src = el.dataset.src || '';
                observer.unobserve(el);
            }
        });
    });
    observer.observe(el);
};
```

### コメントの遅延読み込み
```typescript
const loadComments = async () => {
    if (loading.value || !hasMore.value) return;
    loading.value = true;
    try {
        const response = await commentStore.fetchMoreComments();
        hasMore.value = response.data.length > 0;
    } finally {
        loading.value = false;
    }
};
``` 