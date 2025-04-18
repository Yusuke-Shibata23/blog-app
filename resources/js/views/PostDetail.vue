<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>

    <div v-else-if="post" class="max-w-4xl mx-auto">
      <!-- サムネイル画像 -->
      <div v-if="post.thumbnail_url" class="mb-6">
        <img
          :src="post.thumbnail_url"
          :alt="post.title"
          class="w-full h-64 object-cover rounded-lg"
        />
      </div>

      <!-- タグ一覧 -->
      <div v-if="post.tags && post.tags.length > 0" class="mb-6">
        <div class="flex flex-wrap gap-2">
          <span
            v-for="tagId in post.tags"
            :key="tagId"
            class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
          >
            {{ getTagName(tagId) }}
          </span>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold mb-4">{{ post.title }}</h1>
        <div class="flex items-center text-gray-500 mb-6">
          <span>{{ formatDate(post.created_at) }}</span>
          <span class="mx-2">•</span>
          <span>{{ post.status === 'published' ? '公開' : '下書き' }}</span>
        </div>

        <div class="prose max-w-none" v-html="renderedContent"></div>

        <!-- 画像ギャラリーを一時的にコメントアウト
        <div v-if="post.images && post.images.length > 0" class="mt-8">
          <h2 class="text-xl font-bold mb-4">画像ギャラリー</h2>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div
              v-for="(image, index) in post.images"
              :key="image.id"
              class="relative aspect-w-16 aspect-h-9 cursor-pointer"
              @click="openLightbox(index)"
            >
              <img
                :src="image.url"
                :alt="`画像 ${index + 1}`"
                class="object-cover rounded-lg shadow-md"
              />
            </div>
          </div>
        </div>
        -->

        <div class="mt-8 flex justify-between items-center">
          <div class="flex items-center space-x-4">
            <button
              @click="toggleLike"
              class="flex items-center space-x-2 text-gray-600 hover:text-red-500 transition-colors"
              :class="{ 'text-red-500': isLiked }"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
              </svg>
              <span>{{ likesCount }}</span>
            </button>
          </div>

          <div v-if="canEdit" class="flex space-x-4">
            <button
              @click="editPost"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
            >
              編集
            </button>
            <button
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
import { useAuth } from '@/stores/auth';
import { tags } from '@/config/tags';

const route = useRoute();
const router = useRouter();
const post = ref(null);
const loading = ref(true);
const showLightbox = ref(false);
const currentImageIndex = ref(0);
const isLiked = ref(false);
const likesCount = ref(0);

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
    const isPublicRoute = route.path.includes('/posts/public/');
    const apiUrl = isPublicRoute 
      ? `/api/posts/public/${route.params.id}`
      : `/api/posts/${route.params.id}`;
    const response = await axios.get(apiUrl);
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

const toggleLike = async () => {
  try {
    const response = await axios.post(`/api/posts/${post.value.id}/toggle-like`);
    isLiked.value = response.data.liked;
    likesCount.value = response.data.likes_count;
  } catch (error) {
    console.error('いいねの処理に失敗しました:', error);
  }
};

const checkLikeStatus = async () => {
  try {
    const response = await axios.get(`/api/posts/${post.value.id}/like-status`);
    isLiked.value = response.data.is_liked;
    likesCount.value = response.data.likes_count;
  } catch (error) {
    console.error('いいね状態の取得に失敗しました:', error);
  }
};

const getTagName = (tagId) => {
  return tags[tagId] || '不明なタグ';
};

onMounted(async () => {
  await fetchPost();
  if (post.value) {
    await checkLikeStatus();
  }
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