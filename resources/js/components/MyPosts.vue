<template>
  <div class="my-posts">
    <h2 class="text-2xl font-bold mb-4">マイページ</h2>
    
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
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow"
        @click="handlePostClick(post)"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h2 class="text-xl font-bold mb-2">{{ post.title }}</h2>
            <p class="text-gray-600 mb-4 line-clamp-3">{{ post.content }}</p>
            <div class="flex items-center text-sm text-gray-500">
              <span>投稿者: {{ post.user?.name || '不明' }}</span>
              <span class="mx-2">|</span>
              <span>投稿日: {{ formatDate(post.created_at) }}</span>
              <span v-if="post.published_at" class="mx-2">|</span>
              <span v-if="post.published_at">公開日: {{ formatDate(post.published_at) }}</span>
            </div>
          </div>
          <!-- ログインユーザーのみに表示 -->
          <div v-if="auth.isAuthenticated" class="flex space-x-2 ml-4">
            <button
              @click.stop="$emit('edit', post)"
              class="text-blue-600 hover:text-blue-800"
            >
              編集
            </button>
            <button
              @click.stop="handleDelete(post)"
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
import { ref, onMounted, watch, defineExpose } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuth } from '../stores/auth';

const props = defineProps({
  userId: {
    type: Number,
    required: false
  }
});

const emit = defineEmits(['edit']);

const router = useRouter();

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

const fetchPosts = async () => {
  try {
    loading.value = true;
    let url = '/api/posts/my';
    
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const handleDelete = async (post) => {
  if (!confirm('この投稿を削除しますか？')) return;

  try {
    await axios.delete(`/api/posts/${post.id}`);
    await fetchPosts();
  } catch (error) {
    console.error('投稿の削除に失敗しました:', error);
    alert('投稿の削除に失敗しました。');
  }
};

const handlePostClick = (post) => {
  router.push(`/posts/${post.id}`);
};

watch([currentPage, currentTab], () => {
  fetchPosts();
});

onMounted(() => {
  fetchPosts();
});

// メソッドを外部に公開
defineExpose({
  fetchPosts
});
</script>

<style scoped>
.my-posts {
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