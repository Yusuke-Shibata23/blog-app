<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900">ブログ一覧</h1>
      <router-link
        to="/posts"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
      >
        投稿一覧を見る
      </router-link>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
        @click="router.push(`/posts/${post.id}`)"
      >
        <!-- サムネイル部分 -->
        <div class="aspect-w-16 aspect-h-9">
          <img
            :src="post.thumbnail_url || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085'"
            :alt="post.title"
            class="w-full h-48 object-cover"
            @error="handleImageError"
          />
        </div>
        
        <!-- コンテンツ部分 -->
        <div class="p-6">
          <h2 class="text-xl font-bold mb-2">{{ post.title }}</h2>
          <p class="text-gray-600 mb-4 line-clamp-3">{{ post.excerpt }}</p>
          <div class="flex items-center text-sm text-gray-500">
            <span>投稿者: {{ post.user?.name || '匿名' }}</span>
            <span class="mx-2">|</span>
            <span>投稿日: {{ formatDate(post.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const posts = ref([])
const loading = ref(true)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const handleImageError = (event) => {
  event.target.src = 'https://images.unsplash.com/photo-1498050108023-c5249f4df085'
}

const fetchPosts = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/posts')
    posts.value = response.data.data || []
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPosts()
})
</script>

<style scoped>
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 アスペクト比 */
}

.aspect-w-16 > * {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 