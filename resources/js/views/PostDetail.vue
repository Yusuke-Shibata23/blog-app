<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>

    <div v-else-if="post" class="max-w-4xl mx-auto">
      <!-- サムネイル画像 -->
      <div class="mb-8">
        <img
          :src="post.thumbnail_url || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085'"
          :alt="post.title"
          class="w-full h-96 object-cover rounded-lg shadow-lg"
        />
      </div>

      <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold mb-4">{{ post.title }}</h1>
        <div class="flex items-center text-gray-500 mb-6">
          <span>{{ formatDate(post.created_at) }}</span>
          <span class="mx-2">•</span>
          <span>{{ post.status === 'published' ? '公開' : '下書き' }}</span>
        </div>

        <div class="prose max-w-none">
          <div v-html="renderedContent"></div>
        </div>

        <div v-if="post.images && post.images.length > 0" class="mt-8">
          <h2 class="text-xl font-semibold mb-4">画像ギャラリー</h2>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="(image, index) in post.images" :key="index" class="relative">
              <img
                :src="image.url"
                :alt="`Image ${index + 1}`"
                class="w-full h-48 object-cover rounded-lg cursor-pointer"
                @click="openLightbox(index)"
              />
            </div>
          </div>
        </div>

        <div class="mt-8 flex justify-between items-center">
          <div class="flex space-x-4">
            <button
              v-if="canEdit"
              @click="editPost"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
            >
              編集
            </button>
            <button
              v-if="canDelete"
              @click="deletePost"
              class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors"
            >
              削除
            </button>
          </div>
          <router-link
            to="/posts"
            class="text-gray-600 hover:text-gray-800"
          >
            一覧に戻る
          </router-link>
        </div>
      </div>
    </div>

    <div v-else class="text-center">
      <p class="text-gray-600">記事が見つかりませんでした。</p>
    </div>

    <!-- ライトボックス -->
    <div
      v-if="showLightbox"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
      @click="closeLightbox"
    >
      <div class="relative max-w-4xl w-full mx-4">
        <button
          @click.stop="closeLightbox"
          class="absolute top-4 right-4 text-white hover:text-gray-300"
        >
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <img
          :src="currentImageUrl"
          class="max-h-[80vh] mx-auto"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const route = useRoute();
const router = useRouter();
const post = ref(null);
const loading = ref(true);
const showLightbox = ref(false);
const currentImageIndex = ref(0);

const currentImageUrl = computed(() => {
  return post.value?.images?.[currentImageIndex.value]?.url || '';
});

const canEdit = computed(() => {
  return post.value?.user_id === parseInt(localStorage.getItem('user_id'));
});

const canDelete = computed(() => {
  return post.value?.user_id === parseInt(localStorage.getItem('user_id'));
});

const renderedContent = computed(() => {
  if (!post.value?.content) return '';
  return DOMPurify.sanitize(marked(post.value.content));
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const openLightbox = (index) => {
  currentImageIndex.value = index;
  showLightbox.value = true;
};

const closeLightbox = () => {
  showLightbox.value = false;
};

const fetchPost = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/posts/${route.params.id}`);
    post.value = response.data;
  } catch (error) {
    console.error('Error fetching post:', error);
  } finally {
    loading.value = false;
  }
};

const editPost = () => {
  router.push(`/posts/${post.value.id}/edit`);
};

const deletePost = async () => {
  if (!confirm('この記事を削除してもよろしいですか？')) return;

  try {
    await axios.delete(`/api/posts/${post.value.id}`);
    router.push('/posts');
  } catch (error) {
    console.error('Error deleting post:', error);
    alert('記事の削除に失敗しました。');
  }
};

onMounted(() => {
  fetchPost();
});
</script>

<style scoped>
.prose {
  color: #1f2937;
}

.prose :deep(h1) {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.prose :deep(h2) {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.prose :deep(p) {
  margin-bottom: 1rem;
}

.prose :deep(img) {
  margin: 1rem 0;
  border-radius: 0.5rem;
}

.prose :deep(blockquote) {
  border-left: 4px solid #d1d5db;
  padding-left: 1rem;
  font-style: italic;
}

.prose :deep(code) {
  background-color: #f3f4f6;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
}

.prose :deep(pre) {
  background-color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
}

.prose :deep(ul) {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.prose :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}
</style> 