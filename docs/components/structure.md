# コンポーネント構造

## PostForm.vue

投稿の作成・編集フォームを管理するコンポーネントです。

### Props
| プロパティ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| post | Object | 編集時の投稿データ | 任意 |
| isEditing | Boolean | 編集モードかどうか | 必須 |

### Emits
| イベント名 | 引数 | 説明 |
|------------|------|------|
| close | - | フォームを閉じる |
| success | Object | 保存成功時に投稿データを通知 |
| update:modelValue | Object | フォームデータの更新を通知 |
| cancel | - | キャンセル時に通知 |

### 主要な状態
```javascript
const form = ref({
  title: '',
  content: '',
  status: 'draft',
  scheduled_at: null,
  images: [],
  deletedImages: []
});
```

### メソッド
- `handleSubmit`: フォームの送信処理
- `handleCancel`: キャンセル処理
- `handleImageUpload`: 画像アップロード処理
- `handleImageDelete`: 画像削除処理

## MarkdownEditor.vue

Markdownエディターとプレビューを提供するコンポーネントです。

### Props
| プロパティ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| modelValue | String | エディターの内容 | 必須 |

### Emits
| イベント名 | 引数 | 説明 |
|------------|------|------|
| update:modelValue | String | エディターの内容更新を通知 |

### 主要な状態
```javascript
const content = ref('');
const previewContent = ref('');
```

### メソッド
- `insertMarkdown`: Markdown記法の挿入
- `updatePreview`: プレビューの更新
- `handleInput`: 入力内容の変更処理

## PostList.vue

投稿一覧を表示するコンポーネントです。

### Props
| プロパティ名 | 型 | 説明 | 必須 |
|--------------|-----|------|------|
| posts | Array | 投稿データの配列 | 必須 |
| loading | Boolean | ローディング状態 | 必須 |

### Emits
| イベント名 | 引数 | 説明 |
|------------|------|------|
| edit | Object | 編集ボタンクリック時に投稿データを通知 |
| delete | Number | 削除ボタンクリック時に投稿IDを通知 |

### メソッド
- `handleEdit`: 編集ボタンクリック時の処理
- `handleDelete`: 削除ボタンクリック時の処理
- `formatDate`: 日付のフォーマット処理

## コンポーネント間の関係

```
PostList.vue
  └─ PostForm.vue
       └─ MarkdownEditor.vue
```

- `PostList.vue`は投稿一覧を表示し、編集・削除のトリガーを提供
- `PostForm.vue`は投稿の作成・編集フォームを提供
- `MarkdownEditor.vue`はMarkdownエディターとプレビューを提供 