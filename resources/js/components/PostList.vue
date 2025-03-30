<template>
  <div class="post-list">
    <!-- 検索フィルター -->
    <div class="filters mb-4">
      <div class="flex gap-4">
        <input
          v-model="keyword"
          type="text"
          placeholder="キーワードで検索"
          class="form-input rounded-md"
          @input="debounceSearch"
        />
        <input
          v-model="tagInput"
          type="text"
          placeholder="タグを入力（カンマ区切り）"
          class="form-input rounded-md"
          @input="handleTagInput"
        />
      </div>
    </div>

    <!-- 投稿一覧 -->
    <div class="grid gap-4">
      <div v-for="post in posts" :key="post.id" class="post-card bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-start">
          <h2 class="text-xl font-bold mb-2">{{ post.title }}</h2>
          <div class="flex gap-2" v-if="isAuthor(post)">
            <button
              @click="editPost(post)"
              class="text-blue-600 hover:text-blue-800"
            >
              編集
            </button>
            <button
              @click="deletePost(post)"
              class="text-red-600 hover:text-red-800"
            >
              削除
            </button>
          </div>
        </div>
        <p class="text-gray-600 mb-2">{{ post.content }}</p>
        <div class="flex gap-2 mb-2">
          <span
            v-for="tag in post.tags"
            :key="tag"
            class="bg-gray-200 px-2 py-1 rounded-full text-sm"
          >
            {{ tag }}
          </span>
        </div>
        <div class="text-sm text-gray-500">
          投稿者: {{ post.user.name }}
        </div>
      </div>
    </div>

    <!-- ページネーション -->
    <div class="mt-4 flex justify-center gap-2">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="changePage(page)"
        :class="[
          'px-3 py-1 rounded',
          currentPage === page
            ? 'bg-blue-600 text-white'
            : 'bg-gray-200 hover:bg-gray-300'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import debounce from 'lodash/debounce'

const auth = useAuthStore()
const posts = ref([])
const keyword = ref('')
const tagInput = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

// 検索処理
const fetchPosts = async () => {
  try {
    const params = {
      page: currentPage.value,
      keyword: keyword.value,
      tags: tagInput.value ? tagInput.value.split(',').map(tag => tag.trim()) : []
    }
    
    const response = await axios.get('/api/posts', { params })
    posts.value = response.data.data
    totalPages.value = Math.ceil(response.data.total / response.data.per_page)
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error)
  }
}

// 投稿の削除
const deletePost = async (post) => {
  if (!confirm('この投稿を削除してもよろしいですか？')) return

  try {
    await axios.delete(`/api/posts/${post.id}`)
    await fetchPosts()
  } catch (error) {
    console.error('投稿の削除に失敗しました:', error)
  }
}

// 投稿者かどうかを判定
const isAuthor = (post) => {
  return auth.user && post.user_id === auth.user.id
}

// ページ変更
const changePage = (page) => {
  currentPage.value = page
  fetchPosts()
}

// 検索のデバウンス処理
const debounceSearch = debounce(() => {
  currentPage.value = 1
  fetchPosts()
}, 300)

// タグ入力処理
const handleTagInput = () => {
  currentPage.value = 1
  fetchPosts()
}

onMounted(() => {
  fetchPosts()
})
</script>

<style scoped>
.post-list {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}

.form-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
}
</style> 