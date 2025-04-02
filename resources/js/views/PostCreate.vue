<template>
  <div class="post-create-view">
    <div class="max-w-4xl mx-auto p-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">新規投稿</h1>
        <button
          @click="$router.push('/posts')"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
          キャンセル
        </button>
      </div>

      <PostForm
        :post="{
          title: '',
          content: '',
          status: 'draft',
          scheduled_at: null,
          published_at: null,
          images: []
        }"
        @success="handleSuccess"
        @cancel="handleCancel"
      />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import PostForm from '@/components/PostForm.vue'

const router = useRouter()

const handleSuccess = (post) => {
  router.push(`/posts/${post.id}`)
}

const handleCancel = () => {
  router.push('/posts')
}
</script>

<style scoped>
.post-create-view {
  min-height: calc(100vh - 64px);
  background-color: #f9fafb;
}
</style> 