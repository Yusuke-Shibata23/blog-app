<template>
  <div class="post-form">
    <h2 class="text-2xl font-bold mb-4">
      {{ isEditing ? '投稿を編集' : '新規投稿' }}
    </h2>
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">タイトル</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
      </div>

      <div>
        <label for="content" class="block text-sm font-medium text-gray-700">内容</label>
        <textarea
          id="content"
          v-model="form.content"
          rows="5"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        ></textarea>
      </div>

      <div>
        <label for="tags" class="block text-sm font-medium text-gray-700">
          タグ（カンマ区切りで入力）
        </label>
        <input
          id="tags"
          v-model="tagsInput"
          type="text"
          placeholder="例: 技術, プログラミング, Laravel"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
      </div>

      <div class="flex gap-2">
        <button
          type="submit"
          class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          {{ isEditing ? '更新' : '投稿' }}
        </button>
        <button
          type="button"
          @click="$emit('cancel')"
          class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          キャンセル
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const props = defineProps({
  post: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['submit', 'cancel', 'success'])

const form = ref({
  title: '',
  content: '',
  tags: []
})

const tagsInput = ref('')

const isEditing = computed(() => !!props.post)

// フォームの送信処理
const handleSubmit = async () => {
  try {
    // タグの処理
    const processedTags = tagsInput.value
      .split(',')
      .map(tag => tag.trim())
      .filter(tag => tag)

    const postData = {
      title: form.value.title,
      content: form.value.content,
      tags: processedTags
    }

    console.log('Sending post data:', postData)

    let response
    if (props.post) {
      response = await axios.put(`/api/posts/${props.post.id}`, postData, {
        headers: {
          'Authorization': `Bearer ${auth.token}`
        }
      })
    } else {
      response = await axios.post('/api/posts', postData, {
        headers: {
          'Authorization': `Bearer ${auth.token}`
        }
      })
    }

    // 成功時の処理
    emit('submit', response.data)
    emit('success')

    // フォームをリセット
    form.value = {
      title: '',
      content: '',
      tags: []
    }
    tagsInput.value = ''
  } catch (error) {
    console.error('投稿の保存に失敗しました:', {
      message: error.message,
      response: error.response?.data,
      status: error.response?.status
    })
    
    // エラーメッセージを表示
    const errorMessage = error.response?.data?.message || 
                        error.response?.data?.error || 
                        error.message || 
                        '投稿の保存に失敗しました。'
    alert(errorMessage)
  }
}

// 編集時のフォーム初期化
onMounted(() => {
  if (props.post) {
    form.value = {
      title: props.post.title,
      content: props.post.content,
      tags: props.post.tags || []
    }
    tagsInput.value = form.value.tags.join(', ')
  }
})
</script>

<style scoped>
.post-form {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}
</style> 