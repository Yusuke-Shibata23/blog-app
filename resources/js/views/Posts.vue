<template>
  <div class="posts-view">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">ブログ投稿</h1>
      <router-link
        v-if="auth.isAuthenticated"
        to="/posts/create"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
      >
        新規投稿
      </router-link>
    </div>

    <!-- 投稿フォームモーダル -->
    <div
      v-if="showPostForm"
      class="fixed inset-0 bg-black/50 flex items-center justify-center"
    >
      <div class="bg-white rounded-lg w-full max-w-2xl">
        <PostForm
          :post="selectedPost"
          @success="handlePostSuccess"
          @close="closePostForm"
        />
      </div>
    </div>

    <!-- 投稿一覧 -->
    <MyPosts
      ref="postList"
      @edit="handleEditPost"
      @delete="handleDeletePost"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from '@/stores/auth'
import MyPosts from '@/components/MyPosts.vue'
import PostForm from '@/components/PostForm.vue'
import axios from 'axios'

const auth = useAuth()
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

// 投稿の編集
const handleEditPost = (post) => {
  selectedPost.value = post
  showPostForm.value = true
}

// 投稿の削除
const handleDeletePost = async (post) => {
  if (confirm('この投稿を削除してもよろしいですか？')) {
    try {
      await axios.delete(`/api/posts/${post.id}`)
      if (postList.value) {
        await postList.value.fetchPosts()
      }
      alert('投稿を削除しました。')
    } catch (error) {
      console.error('投稿の削除に失敗しました:', error)
      alert('投稿の削除に失敗しました。')
    }
  }
}

// 投稿の保存成功時の処理
const handlePostSuccess = async (updatedPost) => {
  try {
    // モーダルを閉じる
    showPostForm.value = false
    selectedPost.value = null
    
    // 投稿一覧を再取得
    if (postList.value) {
      await postList.value.fetchPosts()
    }
    
    // 成功メッセージを表示
    alert('投稿を保存しました。')
  } catch (error) {
    console.error('エラーが発生しました:', error)
    alert('エラーが発生しました。')
  }
}
</script>

<style scoped>
.posts-view {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}
</style> 