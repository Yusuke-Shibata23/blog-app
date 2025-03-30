<template>
  <div class="post-detail">
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">読み込み中...</p>
    </div>

    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-600">{{ error }}</p>
      <button
        @click="fetchPost"
        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        再試行
      </button>
    </div>

    <div v-else-if="post" class="max-w-4xl mx-auto px-4 py-8">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- ヘッダー部分 -->
        <div class="p-6 border-b">
          <div class="flex justify-between items-start">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ post.title }}</h1>
              <div class="flex items-center text-sm text-gray-500">
                <span>投稿者: {{ post.user?.name || '不明' }}</span>
                <span class="mx-2">|</span>
                <span>投稿日: {{ formatDate(post.created_at) }}</span>
                <span v-if="post.published_at" class="mx-2">|</span>
                <span v-if="post.published_at">公開日: {{ formatDate(post.published_at) }}</span>
              </div>
            </div>
            <!-- ログインユーザーのみに表示 -->
            <div v-if="auth.isAuthenticated && post.user_id === auth.user.id" class="flex space-x-2">
              <button
                @click="handleEdit"
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
        </div>

        <!-- 画像ギャラリー -->
        <div v-if="post.images && post.images.length > 0" class="p-6 border-b">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="image in post.images"
              :key="image.id"
              class="relative aspect-square group"
            >
              <img
                :src="image.url"
                :alt="image.alt_text || '投稿画像'"
                class="w-full h-full object-cover rounded-lg"
              />
              <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center">
                <button
                  @click="openLightbox(image)"
                  class="text-white opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  拡大表示
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 本文 -->
        <div class="p-6">
          <div class="prose max-w-none" v-html="post.content"></div>
        </div>
      </div>
    </div>

    <!-- 画像ライトボックス -->
    <div
      v-if="selectedImage"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
      @click="closeLightbox"
    >
      <div class="relative max-w-4xl max-h-[90vh] mx-4">
        <img
          :src="selectedImage.url"
          :alt="selectedImage.alt_text || '投稿画像'"
          class="max-w-full max-h-[90vh] object-contain"
        />
        <button
          @click="closeLightbox"
          class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300"
        >
          ×
        </button>
      </div>
    </div>

    <!-- 投稿フォームモーダル -->
    <PostForm
      v-if="showPostForm"
      :post="selectedPost"
      @close="closePostForm"
      @success="handlePostSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuth } from '../stores/auth';
import PostForm from '../components/PostForm.vue';

const route = useRoute();
const router = useRouter();
const auth = useAuth();

const loading = ref(true);
const error = ref(null);
const post = ref(null);
const showPostForm = ref(false);
const selectedPost = ref(null);
const selectedImage = ref(null);

const fetchPost = async () => {
  try {
    loading.value = true;
    error.value = null;
    const response = await axios.get(`/api/posts/${route.params.id}`);
    post.value = response.data;
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error);
    error.value = '投稿の取得に失敗しました。';
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

const handleEdit = () => {
  selectedPost.value = { ...post.value };
  showPostForm.value = true;
};

const handleDelete = async () => {
  if (!confirm('この投稿を削除しますか？')) return;

  try {
    await axios.delete(`/api/posts/${post.value.id}`);
    router.push('/posts');
  } catch (error) {
    console.error('投稿の削除に失敗しました:', error);
    alert('投稿の削除に失敗しました。');
  }
};

const closePostForm = () => {
  showPostForm.value = false;
  selectedPost.value = null;
};

const handlePostSuccess = async () => {
  closePostForm();
  await fetchPost();
};

const openLightbox = (image) => {
  selectedImage.value = image;
};

const closeLightbox = () => {
  selectedImage.value = null;
};

onMounted(() => {
  fetchPost();
});
</script>

<style scoped>
.post-detail {
  min-height: calc(100vh - 4rem);
  background-color: #f3f4f6;
}
</style> 