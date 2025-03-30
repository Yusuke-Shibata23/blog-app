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
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div v-for="post in posts" :key="post.id" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ post.title }}</h2>
        <p class="text-gray-600 mb-4">{{ post.content.substring(0, 100) }}...</p>
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-500">{{ new Date(post.created_at).toLocaleDateString() }}</span>
          <router-link
            :to="'/posts/' + post.id"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            続きを読む
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const posts = ref([])

const fetchPosts = async () => {
  try {
    const response = await axios.get('/api/posts')
    posts.value = response.data.data || []
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error)
  }
}

onMounted(() => {
  fetchPosts()
})
</script> 