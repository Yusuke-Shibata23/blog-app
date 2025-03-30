<template>
  <div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-6">{{ isEditing ? '投稿を編集' : '新規投稿' }}</h2>
    
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">タイトル</label>
        <input
          type="text"
          id="title"
          v-model="form.title"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          required
        />
      </div>

      <div>
        <label for="content" class="block text-sm font-medium text-gray-700">本文</label>
        <textarea
          id="content"
          v-model="form.content"
          rows="6"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          required
        ></textarea>
      </div>

      <div>
        <label for="status" class="block text-sm font-medium text-gray-700">ステータス</label>
        <select
          id="status"
          v-model="form.status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option value="draft">下書き</option>
          <option value="published">公開</option>
        </select>
      </div>

      <div v-if="form.status === 'published'">
        <label for="scheduled_at" class="block text-sm font-medium text-gray-700">公開予定日時</label>
        <input
          type="datetime-local"
          id="scheduled_at"
          v-model="form.scheduled_at"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">画像</label>
        <div class="mt-1 flex items-center space-x-4">
          <div v-for="(image, index) in form.images" :key="index" class="relative">
            <img
              :src="image.preview || image.image_path"
              class="h-32 w-32 object-cover rounded-lg"
              alt="投稿画像"
            />
            <button
              type="button"
              @click="removeImage(index)"
              class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1"
            >
              ×
            </button>
          </div>
          <div v-if="form.images.length < 5" class="relative">
            <input
              type="file"
              accept="image/*"
              @change="handleImageUpload"
              class="hidden"
              ref="imageInput"
            />
            <button
              type="button"
              @click="$refs.imageInput.click()"
              class="h-32 w-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-500 hover:border-indigo-500 hover:text-indigo-500"
            >
              <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div class="flex justify-end space-x-4">
        <button
          type="button"
          @click="handleCancel"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          キャンセル
        </button>
        <button
          type="submit"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="loading"
        >
          {{ loading ? '保存中...' : (isEditing ? '更新' : '投稿') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  post: {
    type: Object,
    default: () => ({
      title: '',
      content: '',
      status: 'draft',
      scheduled_at: null,
      images: []
    })
  }
});

const emit = defineEmits(['close', 'success']);

const loading = ref(false);
const imageInput = ref(null);

const form = ref({
  title: props.post.title,
  content: props.post.content,
  status: props.post.status,
  scheduled_at: props.post.scheduled_at,
  images: props.post.images || []
});

const isEditing = computed(() => !!props.post.id);

const handleImageUpload = (event) => {
  const files = event.target.files;
  if (files.length > 0) {
    const file = files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
      form.value.images.push({
        file,
        preview: e.target.result
      });
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = (index) => {
  form.value.images.splice(index, 1);
};

const handleCancel = () => {
  // フォームをリセット
  form.value = {
    title: props.post.title,
    content: props.post.content,
    status: props.post.status,
    scheduled_at: props.post.scheduled_at,
    images: props.post.images || []
  };
  emit('close');
};

const handleSubmit = async () => {
  try {
    loading.value = true;
    const formData = new FormData();
    
    // 基本データの追加
    formData.append('title', form.value.title);
    formData.append('content', form.value.content);
    formData.append('status', form.value.status);
    
    // scheduled_atの追加（値がある場合のみ）
    if (form.value.scheduled_at) {
      formData.append('scheduled_at', form.value.scheduled_at);
    }

    // 新しい画像の追加
    form.value.images.forEach((image, index) => {
      if (image.file) {
        formData.append(`images[${index}]`, image.file);
      }
    });

    // デバッグ用のログ
    console.log('送信するデータ:', {
      title: form.value.title,
      content: form.value.content,
      status: form.value.status,
      scheduled_at: form.value.scheduled_at,
      images: form.value.images
    });

    // FormDataの内容を確認
    for (let pair of formData.entries()) {
      console.log(pair[0] + ': ' + pair[1]);
    }

    const headers = {
      'Accept': 'application/json'
    };

    let response;
    if (isEditing.value) {
      response = await axios.put(`/api/posts/${props.post.id}`, formData, { headers });
      console.log('更新成功:', response.data);
    } else {
      response = await axios.post('/api/posts', formData, { headers });
      console.log('作成成功:', response.data);
    }

    // 成功時の処理
    emit('success', response.data);
  } catch (error) {
    console.error('投稿の保存に失敗しました:', error);
    if (error.response) {
      // サーバーからのエラーレスポンス
      console.error('エラーレスポンス:', error.response.data);
      const errorMessage = error.response.data.message || '投稿の保存に失敗しました。';
      const validationErrors = error.response.data.errors;
      
      if (validationErrors) {
        // バリデーションエラーの場合
        alert('入力内容を確認してください。\n' + Object.values(validationErrors).flat().join('\n'));
      } else {
        // その他のエラーの場合
        alert(errorMessage);
      }
    } else if (error.request) {
      // リクエストは送信されたがレスポンスがない場合
      alert('サーバーとの通信に失敗しました。');
    } else {
      // リクエストの作成に失敗した場合
      alert('リクエストの作成に失敗しました。');
    }
  } finally {
    loading.value = false;
  }
};
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