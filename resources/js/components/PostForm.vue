<template>
  <div class="post-form bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-bold mb-4">{{ isEditing ? '投稿を編集' : '新規投稿' }}</h2>
    <form @submit.prevent="handleSubmit">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
          タイトル
        </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          class="form-input w-full"
          required
        />
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="content">
          本文
        </label>
        <textarea
          id="content"
          v-model="form.content"
          class="form-textarea w-full h-32"
          required
        ></textarea>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="tags">
          タグ（カンマ区切り）
        </label>
        <input
          id="tags"
          v-model="form.tags"
          type="text"
          class="form-input w-full"
          placeholder="例: 技術, プログラミング, 開発"
        />
      </div>

      <div class="flex justify-end gap-4">
        <button
          type="button"
          @click="$emit('cancel')"
          class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
        >
          キャンセル
        </button>
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
        >
          {{ isEditing ? '更新' : '投稿' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const props = defineProps({
  post: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['success', 'cancel'])

const auth = useAuthStore()

const isEditing = computed(() => !!props.post)

const form = ref({
  title: '',
  content: '',
  tags: ''
})

// 編集時は既存のデータを設定
if (props.post) {
  form.value = {
    title: props.post.title,
    content: props.post.content,
    tags: props.post.tags ? props.post.tags.join(', ') : ''
  }
}

const handleSubmit = async () => {
  try {
    const postData = {
      title: form.value.title,
      content: form.value.content,
      tags: form.value.tags ? form.value.tags.split(',').map(tag => tag.trim()).filter(tag => tag) : []
    }

    console.log('送信するデータ:', postData)

    if (isEditing.value) {
      await axios.put(`/api/posts/${props.post.id}`, postData, {
        headers: {
          'Authorization': `Bearer ${auth.token}`
        }
      })
    } else {
      await axios.post('/api/posts', postData, {
        headers: {
          'Authorization': `Bearer ${auth.token}`
        }
      })
    }

    emit('success')
  } catch (error) {
    console.error('投稿の保存に失敗しました:', error)
    alert(error.response?.data?.message || '投稿の保存に失敗しました。')
  }
}
</script>

<style scoped>
.form-input,
.form-textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.375rem;
}

.form-textarea {
  resize: vertical;
}
</style> 