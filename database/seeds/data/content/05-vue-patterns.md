# Vue.jsのコンポーネント設計パターン

## はじめに

Vue.jsでの効果的なコンポーネント設計は、アプリケーションの保守性と再利用性を高めるために重要です。この記事では、Vue.jsにおける主要なコンポーネント設計パターンについて解説します。

![Vue.jsコンポーネントの構造](/storage/posts/5/component-structure.png)
*Vue.jsコンポーネントの推奨される構造*

## コンポーネントの基本構造

### 1. 単一ファイルコンポーネント（SFC）

```vue
<template>
  <div class="user-profile">
    <h2>{{ user.name }}</h2>
    <p>{{ user.email }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const user = ref({
  name: 'John Doe',
  email: 'john@example.com'
})
</script>

<style scoped>
.user-profile {
  padding: 1rem;
  border: 1px solid #ddd;
}
</style>
```

## 主要な設計パターン

### 1. Compositionパターン

```vue
<script setup>
import { useUser } from '@/composables/useUser'
import { useNotification } from '@/composables/useNotification'

const { user, updateUser } = useUser()
const { notify } = useNotification()

const handleUpdate = async () => {
  await updateUser()
  notify('更新が完了しました')
}
</script>
```

### 2. Renderlessコンポーネント

```vue
<template>
  <slot :data="data" :loading="loading" :error="error">
    <!-- デフォルトコンテンツ -->
  </slot>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const data = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    // データの取得
    loading.value = false
  } catch (e) {
    error.value = e
  }
})
</script>
```

## コンポーネント間の通信

### 1. Props/Emitsパターン

```vue
<template>
  <button @click="handleClick">
    {{ label }}
  </button>
</template>

<script setup>
const props = defineProps({
  label: String
})

const emit = defineEmits(['click'])

const handleClick = () => {
  emit('click')
}
</script>
```

### 2. Provide/Injectパターン

```vue
<script setup>
import { provide, ref } from 'vue'

const theme = ref('light')
provide('theme', theme)
</script>
```

## 状態管理パターン

### 1. Piniaストア

```javascript
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    loading: false
  }),
  actions: {
    async fetchUser() {
      this.loading = true
      try {
        // ユーザー情報の取得
      } finally {
        this.loading = false
      }
    }
  }
})
```

## パフォーマンス最適化

1. コンポーネントの遅延ロード
2. メモ化
3. 仮想スクロール
4. キャッシュ戦略

## テストパターン

```javascript
import { mount } from '@vue/test-utils'
import UserProfile from './UserProfile.vue'

test('ユーザー情報が正しく表示される', () => {
  const wrapper = mount(UserProfile, {
    props: {
      user: {
        name: 'John Doe',
        email: 'john@example.com'
      }
    }
  })

  expect(wrapper.text()).toContain('John Doe')
})
```

## まとめ

Vue.jsのコンポーネント設計パターンを適切に活用することで、保守性が高く、再利用可能なコードを書くことができます。状況に応じて最適なパターンを選択し、アプリケーションの品質を向上させましょう。

## 参考リンク

- [Vue.js公式ドキュメント](https://vuejs.org/)
- [Vue.jsスタイルガイド](https://vuejs.org/style-guide/)
- [Pinia公式ドキュメント](https://pinia.vuejs.org/) 