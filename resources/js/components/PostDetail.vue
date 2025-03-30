<template>
  <div class="post-detail">
    <div v-if="loading" class="text-center py-4">
      読み込み中...
    </div>
    <div v-else-if="error" class="text-center py-4 text-red-600">
      {{ error }}
    </div>
    <div v-else class="bg-white rounded-lg shadow-md p-6">
      <div class="flex justify-between items-start mb-6">
        <div>
          <h1 class="text-3xl font-bold mb-2">{{ post.title }}</h1>
          <div class="flex items-center text-sm text-gray-500">
            <span>投稿者: {{ post.user?.name || '不明' }}</span>
            <span class="mx-2">|</span>
            <span>投稿日: {{ formatDate(post.created_at) }}</span>
            <span v-if="post.published_at" class="mx-2">|</span>
            <span v-if="post.published_at">公開日: {{ formatDate(post.published_at) }}</span>
          </div>
        </div>
        <!-- ログインユーザーのみに表示 -->
        <div v-if="auth.isAuthenticated && auth.user?.id === post.user_id" class="flex space-x-2">
          <button
            @click="$emit('edit', post)"
            class="text-blue-600 hover:text-blue-800"
          >
            編集
          </button>
          <button
            @click="handleDelete"
            class="text-red-600 hover:text-red-800"
          >
            削除
          </button>
        </div>
      </div>

      <!-- 画像ギャラリー -->
      <div v-if="post.images && post.images.length > 0" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="image in post.images" :key="image.id" class="relative">
            <img
              :src="`/storage/${image.image_path}`"
              :alt="post.title"
              class="w-full h-48 object-cover rounded-lg"
            />
          </div>
        </div>
      </div>

      <!-- 本文 -->
      <div class="prose max-w-none">
        {{ post.content }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuth } from '../stores/auth';

const props = defineProps({
  postId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['edit', 'delete']);

const loading = ref(true);
const error = ref(null);
const post = ref(null);
const auth = useAuth();

const fetchPost = async () => {
  try {
    loading.value = true;
    error.value = null;
    const response = await axios.get(`/api/posts/${props.postId}`);
    post.value = response.data;
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error);
    error.value = error.response?.data?.message || '投稿の取得に失敗しました。';
  } finally {
    loading.value = false;
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const handleDelete = async () => {
  if (!confirm('この投稿を削除しますか？')) return;

  try {
    await axios.delete(`/api/posts/${props.postId}`);
    emit('delete');
  } catch (error) {
    console.error('投稿の削除に失敗しました:', error);
    alert('投稿の削除に失敗しました。');
  }
};

onMounted(() => {
  fetchPost();
});
</script>

<style scoped>
.post-detail {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}
</style> 