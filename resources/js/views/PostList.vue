<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">記事一覧</h1>
      <router-link
        to="/posts/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        新規投稿
      </router-link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
      >
        <div class="aspect-w-16 aspect-h-9">
          <img
            :src="post.thumbnail_url || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085'"
            :alt="post.title"
            class="w-full h-48 object-cover"
          />
        </div>
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-2 line-clamp-2">{{ post.title }}</h2>
          <p class="text-gray-600 mb-4 line-clamp-3">{{ post.excerpt }}</p>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">{{ formatDate(post.created_at) }}</span>
            <router-link
              :to="'/posts/' + post.id"
              class="text-indigo-600 hover:text-indigo-800 font-medium"
            >
              続きを読む
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const posts = ref([]);
const loading = ref(true);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const fetchPosts = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/posts');
    posts.value = response.data;
  } catch (error) {
    console.error('Error fetching posts:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPosts();
});
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

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 