<template>
  <div class="post-list">
    <!-- ログインユーザーのみに表示 -->
    <div v-if="auth.isAuthenticated" class="mb-4">
      <div class="flex space-x-2">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="currentTab = tab.value"
          :class="[
            'px-4 py-2 rounded-md',
            currentTab === tab.value
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- 投稿一覧 -->
    <div v-if="loading" class="text-center py-4">
      読み込み中...
    </div>
    <div v-else-if="posts.length === 0" class="text-center py-4">
      投稿がありません。
    </div>
    <div v-else class="space-y-4">
      <div
        v-for="post in filteredPosts"
        :key="post.id"
        class="bg-white rounded-lg shadow-md p-6"
      >
        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-xl font-bold mb-2">{{ post.title }}</h2>
            <p class="text-gray-600 mb-4">{{ post.content }}</p>
            <div class="flex items-center text-sm text-gray-500">
              <span>投稿者: {{ post.user?.name || '不明' }}</span>
              <span class="mx-2">|</span>
              <span>投稿日: {{ formatDate(post.created_at) }}</span>
              <span v-if="post.published_at" class="mx-2">|</span>
              <span v-if="post.published_at">公開日: {{ formatDate(post.published_at) }}</span>
            </div>
          </div>
          <!-- ログインユーザーのみに表示 -->
          <div v-if="auth.isAuthenticated" class="flex space-x-2">
            <button
              @click="$emit('edit', post)"
              class="text-blue-600 hover:text-blue-800"
            >
              編集
            </button>
            <button
              @click="handleDelete(post)"
              class="text-red-600 hover:text-red-800"
            >
              削除
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ページネーション -->
    <div v-if="auth.isAuthenticated && totalPages > 1" class="mt-4 flex justify-center">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="currentPage = page"
        :class="[
          'mx-1 px-3 py-1 rounded',
          currentPage === page
            ? 'bg-blue-600 text-white'
            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { useAuth } from '../stores/auth';

const props = defineProps({
  userId: {
    type: Number,
    required: false
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
  { label: '公開中', value: 'published' },
  { label: '下書き', value: 'draft' },
  { label: '予約投稿', value: 'scheduled' }
];

const auth = useAuth();

// フィルタリングされた投稿一覧を計算
const filteredPosts = computed(() => {
  // nullの投稿を除外
  const validPosts = posts.value.filter(post => post !== null);

  if (!auth.isAuthenticated) {
    return validPosts.filter(post => post.status === 'published');
  }

  switch (currentTab.value) {
    case 'draft':
      return validPosts.filter(post => post.status === 'draft');
    case 'scheduled':
      return validPosts.filter(post => post.status === 'scheduled');
    default:
      return validPosts;
  }
});

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
      case 'published':
        url = '/api/posts/published';
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
    alert('投稿の取得に失敗しました。');
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