<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">投稿一覧</h2>
      <div class="flex space-x-4">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="currentTab = tab.value"
          :class="[
            'px-4 py-2 rounded-md text-sm font-medium',
            currentTab === tab.value
              ? 'bg-indigo-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
    </div>

    <div v-else-if="posts.length === 0" class="text-center py-8 text-gray-500">
      投稿がありません
    </div>

    <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-lg shadow overflow-hidden"
      >
        <div v-if="post.images && post.images.length > 0" class="relative h-48">
          <img
            :src="post.images[0].image_path"
            :alt="post.title"
            class="w-full h-full object-cover"
          />
          <div
            v-if="post.images.length > 1"
            class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-sm"
          >
            +{{ post.images.length - 1 }}
          </div>
        </div>

        <div class="p-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-900">{{ post.title }}</h3>
            <span
              :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                getStatusClass(post.status)
              ]"
            >
              {{ getStatusLabel(post.status) }}
            </span>
          </div>

          <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ post.content }}</p>

          <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center space-x-2">
              <span>{{ post.user.name }}</span>
              <span>•</span>
              <span>{{ formatDate(post.created_at) }}</span>
            </div>

            <div class="flex items-center space-x-2">
              <button
                v-if="post.status === 'draft'"
                @click="publishPost(post)"
                class="text-green-600 hover:text-green-700"
              >
                公開
              </button>
              <button
                @click="$emit('edit', post)"
                class="text-indigo-600 hover:text-indigo-700"
              >
                編集
              </button>
              <button
                @click="deletePost(post)"
                class="text-red-600 hover:text-red-700"
              >
                削除
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="posts.length > 0" class="flex justify-center mt-6">
      <button
        v-if="currentPage > 1"
        @click="currentPage--"
        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
      >
        前へ
      </button>
      <span class="px-4 py-2 text-sm text-gray-700">
        {{ currentPage }} / {{ totalPages }}
      </span>
      <button
        v-if="currentPage < totalPages"
        @click="currentPage++"
        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
      >
        次へ
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  userId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['edit']);

const loading = ref(false);
const posts = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const currentTab = ref('all');

const tabs = [
  { label: 'すべて', value: 'all' },
  { label: '下書き', value: 'draft' },
  { label: '予約投稿', value: 'scheduled' }
];

const fetchPosts = async () => {
  try {
    loading.value = true;
    let url = '/api/posts';
    
    switch (currentTab.value) {
      case 'draft':
        url = '/api/posts/drafts';
        break;
      case 'scheduled':
        url = '/api/posts/scheduled';
        break;
    }

    const response = await axios.get(url, {
      params: {
        page: currentPage.value
      }
    });

    posts.value = response.data.data;
    currentPage.value = response.data.current_page;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error('投稿の取得に失敗しました:', error);
  } finally {
    loading.value = false;
  }
};

const getStatusClass = (status) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-800';
    case 'draft':
      return 'bg-gray-100 text-gray-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const getStatusLabel = (status) => {
  switch (status) {
    case 'published':
      return '公開中';
    case 'draft':
      return '下書き';
    default:
      return status;
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const publishPost = async (post) => {
  if (!confirm('この投稿を公開しますか？')) return;

  try {
    await axios.put(`/api/posts/${post.id}`, {
      ...post,
      status: 'published',
      published_at: new Date().toISOString()
    });
    await fetchPosts();
  } catch (error) {
    console.error('投稿の公開に失敗しました:', error);
    alert('投稿の公開に失敗しました。');
  }
};

const deletePost = async (post) => {
  if (!confirm('この投稿を削除しますか？')) return;

  try {
    await axios.delete(`/api/posts/${post.id}`);
    await fetchPosts();
  } catch (error) {
    console.error('投稿の削除に失敗しました:', error);
    alert('投稿の削除に失敗しました。');
  }
};

watch([currentPage, currentTab], () => {
  fetchPosts();
});

onMounted(() => {
  fetchPosts();
});
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