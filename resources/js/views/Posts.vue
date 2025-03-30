<template>
  <div class="posts-view">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">ブログ投稿</h1>
      <button
        v-if="auth.isAuthenticated"
        @click="showPostForm = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
      >
        新規投稿
      </button>
    </div>

    <!-- 投稿フォームモーダル -->
    <div
      v-if="showPostForm"
      class="fixed inset-0 bg-black/50 flex items-center justify-center"
    >
      <div class="bg-white rounded-lg w-full max-w-2xl">
        <PostForm
          :post="selectedPost"
          @submit="handlePostSubmit"
          @success="handlePostSuccess"
          @cancel="closePostForm"
        />
      </div>
    </div>

    <!-- 投稿一覧 -->
    <PostList
      ref="postList"
      @edit="handleEditPost"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import PostList from '@/components/PostList.vue'
import PostForm from '@/components/PostForm.vue'

const auth = useAuthStore()
const showPostForm = ref(false)
const selectedPost = ref(null)
const postList = ref(null)

// 認証状態の確認
onMounted(async () => {
  if (!auth.isAuthenticated) {
    await auth.checkAuth()
  }
})

// 投稿フォームを閉じる
const closePostForm = () => {
  showPostForm.value = false
  selectedPost.value = null
}

// 投稿の保存完了時の処理
const handlePostSubmit = () => {
  // フォームはhandlePostSuccessで閉じるため、ここでは何もしない
}

// 投稿の保存成功時の処理
const handlePostSuccess = async () => {
  try {
    if (postList.value) {
      await postList.value.fetchPosts()
      closePostForm() // フォームを閉じる
    } else {
      console.error('PostListコンポーネントが見つかりません')
    }
  } catch (error) {
    console.error('投稿一覧の更新に失敗しました:', error)
    alert('投稿一覧の更新に失敗しました。')
  }
}

// 投稿の編集
const handleEditPost = (post) => {
  selectedPost.value = post
  showPostForm.value = true
}
</script>

<style scoped>
.posts-view {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}
</style> 