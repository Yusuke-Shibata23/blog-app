# PostDetail.vueの技術解説

## はじめに

`PostDetail.vue`は、ブログ投稿の詳細を表示するコンポーネントです。このコンポーネントでは、Vue.jsの最新機能と様々なライブラリを組み合わせて、リッチなユーザー体験を実現しています。

## 使用している主要な技術

### 1. Vue 3 Composition API

```javascript
import { ref, computed, onMounted } from 'vue';
```

これは：
- Vue 3の新しいAPIスタイル
- ロジックの再利用性が高い
- 型推論が容易
- コードの整理がしやすい

具体的な使用例：
```javascript
const post = ref(null);  // リアクティブなデータ
const loading = ref(true);  // ローディング状態
const showLightbox = ref(false);  // ライトボックスの表示状態
```

### 2. リアクティブな状態管理

```javascript
const renderedContent = computed(() => {
  if (!post.value?.content) return '';
  return DOMPurify.sanitize(marked(post.value.content));
});
```

これは：
- データの変更を自動的に検知
- 依存関係に基づいて再計算
- パフォーマンスの最適化
- キャッシュ機能

### 3. ライフサイクルフック

```javascript
onMounted(async () => {
  await fetchPost();
  if (post.value) {
    await checkLikeStatus();
  }
});
```

これは：
- コンポーネントのマウント時に実行
- 非同期処理のサポート
- 初期データの取得
- イベントリスナーの設定

### 4. ルーティング管理

```javascript
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
```

これは：
- URLパラメータの取得
- プログラム的な画面遷移
- ルート情報へのアクセス
- ナビゲーションガード

### 5. HTTP通信（Axios）

```javascript
import axios from 'axios';

const fetchPost = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/posts/${route.params.id}`);
    post.value = response.data;
  } catch (error) {
    console.error('Error fetching post:', error);
  } finally {
    loading.value = false;
  }
};
```

これは：
- PromiseベースのHTTPクライアント
- エラーハンドリング
- リクエスト/レスポンスのインターセプト
- 自動的なJSON変換

### 6. Markdown処理

```javascript
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const renderedContent = computed(() => {
  return DOMPurify.sanitize(marked(post.value.content));
});
```

これは：
- MarkdownテキストのHTML変換
- XSS攻撃の防止
- セキュアなHTML出力
- カスタムレンダラーのサポート

### 7. コンポーネント間通信

```javascript
const canEdit = computed(() => {
  return post.value?.user_id === parseInt(localStorage.getItem('user_id'));
});
```

これは：
- ローカルストレージの利用
- ユーザー認証状態の管理
- 権限チェック
- リアクティブな権限管理

### 8. スタイリング（Scoped CSS）

```css
<style scoped>
.prose {
  color: #1f2937;
}

.prose :deep(h1) {
  font-size: 1.5rem;
  font-weight: 700;
}
</style>
```

これは：
- コンポーネント固有のスタイル
- CSSのカプセル化
- 子コンポーネントへのスタイル適用
- テーマの一貫性

## 実装の特徴

### 1. エラーハンドリング

```javascript
try {
  await axios.delete(`/api/posts/${post.value.id}`);
  router.push('/posts');
} catch (error) {
  console.error('Error deleting post:', error);
  alert('記事の削除に失敗しました。');
}
```

これは：
- エラーの適切な処理
- ユーザーへのフィードバック
- アプリケーションの安定性
- デバッグのしやすさ

### 2. パフォーマンス最適化

```javascript
const currentImageUrl = computed(() => {
  return post.value?.images?.[currentImageIndex.value]?.url || '';
});
```

これは：
- メモ化による再計算の防止
- 条件付きレンダリング
- 遅延読み込み
- リソースの効率的な使用

### 3. ユーザー体験の向上

```javascript
const toggleLike = async () => {
  try {
    const response = await axios.post(`/api/posts/${post.value.id}/toggle-like`);
    isLiked.value = response.data.liked;
    likesCount.value = response.data.likes_count;
  } catch (error) {
    console.error('いいねの処理に失敗しました:', error);
  }
};
```

これは：
- 即時のフィードバック
- スムーズなインタラクション
- エラーの適切な処理
- 状態の一貫性

## まとめ

このコンポーネントは：

1. **モダンな技術スタック**
   - Vue 3 Composition API
   - リアクティブプログラミング
   - モジュール化された設計

2. **堅牢な実装**
   - 適切なエラーハンドリング
   - セキュリティ対策
   - パフォーマンス最適化

3. **優れたユーザー体験**
   - スムーズなインタラクション
   - 即時のフィードバック
   - レスポンシブなデザイン

これらの特徴により、モダンで使いやすいブログ投稿詳細画面を実現しています。 
