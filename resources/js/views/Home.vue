<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">記事一覧</h1>
        <div class="flex space-x-4">
          <button
            @click="activeTab = 'all'"
            class="px-4 py-2 rounded-md"
            :class="activeTab === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
          >
            すべての記事
          </button>
          <button
            @click="activeTab = 'liked'"
            class="px-4 py-2 rounded-md"
            :class="activeTab === 'liked' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
          >
            いいねした記事
          </button>
        </div>
      </div>
      <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="記事を検索..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="handleSearch"
          />
        </div>
      </div>

      <!-- タグフィルター -->
      <div class="mb-6">
        <div class="flex flex-wrap gap-2">
          <button
            v-for="(name, id) in tags"
            :key="id"
            @click="toggleTagFilter(Number(id))"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-colors',
              selectedTags.includes(Number(id))
                ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ name }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
    </div>

    <div v-else-if="filteredPosts.length === 0" class="text-center py-12">
      <p class="text-gray-500">
        {{ activeTab === 'liked' ? 'いいねした記事はありません。' : '記事が見つかりませんでした。' }}
      </p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="post in filteredPosts"
        :key="post.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
        @click="handlePostClick(post.id)"
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
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { tags } from '@/config/tags'

const router = useRouter()
const posts = ref([])
const loading = ref(true)
const searchQuery = ref('')
const activeTab = ref('all')
const selectedTags = ref([])
let searchTimeout = null

// フィルタリングされた投稿を計算
const filteredPosts = computed(() => {
  if (selectedTags.value.length === 0) {
    return posts.value;
  }
  
  return posts.value.filter(post => {
    if (!post.tags || post.tags.length === 0) {
      return false;
    }
    return selectedTags.value.every(tagId => post.tags.includes(tagId));
  });
});

// タグフィルターの切り替え
const toggleTagFilter = (tagId) => {
  const index = selectedTags.value.indexOf(tagId);
  if (index === -1) {
    selectedTags.value.push(tagId);
  } else {
    selectedTags.value.splice(index, 1);
  }
};

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

const handlePostClick = (postId) => {
  router.push(`/posts/public/${postId}`)
}

const fetchPosts = async (query = '') => {
  try {
    loading.value = true
    const endpoint = activeTab.value === 'liked' ? '/api/posts/liked' : '/api/posts'
    const response = await axios.get(endpoint, {
      params: {
        search: query
      }
    })
    posts.value = response.data.data || []
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchPosts(searchQuery.value)
  }, 300)
}

watch(activeTab, () => {
  fetchPosts(searchQuery.value)
})

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