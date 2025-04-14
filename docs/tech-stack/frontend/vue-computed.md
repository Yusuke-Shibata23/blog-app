# Vue.jsのcomputed解説

## はじめに

`computed`は、Vue.jsの重要な機能の1つで、リアクティブなデータの計算や変換を行うためのプロパティです。通常のメソッドとは異なり、依存するデータが変更された場合のみ再計算されるという特徴があります。

## 基本的な使い方

### 1. シンプルな計算

```javascript
const count = ref(0);
const doubleCount = computed(() => count.value * 2);
```

これは：
- `count`の値が変更されるたびに自動的に再計算
- キャッシュ機能により、同じ値の場合は再計算をスキップ
- テンプレート内で`{{ doubleCount }}`のように使用可能

### 2. 条件付きの計算

```javascript
const isEven = computed(() => count.value % 2 === 0);
```

これは：
- 偶数の場合に`true`を返す
- リアクティブな条件チェック
- テンプレート内で`v-if="isEven"`のように使用可能

## 実際の使用例

### 1. 投稿の表示制御

```javascript
const canEdit = computed(() => {
  return post.value?.user_id === parseInt(localStorage.getItem('user_id'));
});
```

これは：
- ユーザーが投稿を編集できるかどうかを判定
- 投稿の所有者と現在のユーザーを比較
- リアクティブな権限チェック

### 2. Markdownのレンダリング

```javascript
const renderedContent = computed(() => {
  if (!post.value?.content) return '';
  return DOMPurify.sanitize(marked(post.value.content));
});
```

これは：
- MarkdownテキストをHTMLに変換
- セキュリティ対策としてサニタイズ
- コンテンツの変更を自動的に検知

### 3. 画像URLの取得

```javascript
const currentImageUrl = computed(() => {
  return post.value?.images?.[currentImageIndex.value]?.url || '';
});
```

これは：
- 現在表示中の画像URLを取得
- オプショナルチェーンによる安全なアクセス
- デフォルト値の設定

## computedの特徴

### 1. キャッシュ機能

```javascript
const expensiveCalculation = computed(() => {
  console.log('計算実行');
  return someValue.value * 2;
});
```

これは：
- 依存する値が変更されない限り再計算しない
- パフォーマンスの最適化
- 不要な計算の回避

### 2. 依存関係の追跡

```javascript
const fullName = computed(() => {
  return `${firstName.value} ${lastName.value}`;
});
```

これは：
- `firstName`と`lastName`の変更を自動的に検知
- 必要な場合のみ再計算
- 効率的な更新処理

### 3. ゲッターとセッター

```javascript
const fullName = computed({
  get() {
    return `${firstName.value} ${lastName.value}`;
  },
  set(newValue) {
    const [first, last] = newValue.split(' ');
    firstName.value = first;
    lastName.value = last;
  }
});
```

これは：
- 値の取得と設定の両方を制御
- 双方向バインディングの実現
- データの整合性を保証

## メソッドとの違い

### 1. キャッシュの有無

```javascript
// computed
const doubleCount = computed(() => count.value * 2);

// メソッド
const doubleCountMethod = () => count.value * 2;
```

これは：
- `computed`は依存値が変更されない限り再計算しない
- メソッドは呼び出されるたびに再計算
- パフォーマンスに影響

### 2. 使用シーン

```javascript
// computed: 値の計算や変換
const formattedDate = computed(() => {
  return new Date(date.value).toLocaleDateString();
});

// メソッド: イベントハンドリング
const handleClick = () => {
  // 何らかの処理
};
```

これは：
- `computed`: データの変換や計算
- メソッド: ユーザーアクションへの応答

## まとめ

`computed`は：

1. **効率的なデータ処理**
   - 自動的な再計算
   - キャッシュ機能
   - パフォーマンス最適化

2. **コードの可読性**
   - 宣言的な記述
   - ロジックの集約
   - メンテナンス性の向上

3. **リアクティブな処理**
   - 依存関係の自動追跡
   - データの整合性
   - 予測可能な動作

これらの特徴により、Vue.jsアプリケーションの開発を効率的かつ安全に行うことができます。 
