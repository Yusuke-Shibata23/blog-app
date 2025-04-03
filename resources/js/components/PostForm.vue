<template>
  <div class="max-w-4xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6">{{ isEditing ? '投稿を編集' : '新規投稿' }}</h2>
    
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">タイトル</label>
        <input
          type="text"
          id="title"
          v-model="form.title"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          required
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">サムネイル画像</label>
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
          <div class="space-y-1 text-center">
            <svg
              class="mx-auto h-12 w-12 text-gray-400"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 48 48"
              aria-hidden="true"
            >
              <path
                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            <div class="flex text-sm text-gray-600">
              <label
                for="thumbnail"
                class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
              >
                <span>サムネイル画像をアップロード</span>
                <input
                  id="thumbnail"
                  name="thumbnail"
                  type="file"
                  class="sr-only"
                  accept="image/*"
                  @change="handleThumbnailChange"
                />
              </label>
              <p class="pl-1">またはドラッグ＆ドロップ</p>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
          </div>
        </div>
        <div v-if="form.thumbnail" class="mt-4">
          <img :src="form.thumbnail.url.replace(/\/storage\/+/g, '/storage/')" class="w-full h-48 object-cover rounded-lg" />
          <button
            type="button"
            @click="removeThumbnail"
            class="mt-2 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            サムネイルを削除
          </button>
        </div>
      </div>

      <div>
        <label for="content" class="block text-sm font-medium text-gray-700">内容</label>
        <MarkdownEditor
          v-model="form.content"
          class="mt-1"
        />
      </div>
      <!-- 画像アップロードフォームを一時的にコメントアウト
      <div>
        <label class="block text-sm font-medium text-gray-700">画像</label>
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
          <div class="space-y-1 text-center">
            <svg
              class="mx-auto h-12 w-12 text-gray-400"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 48 48"
              aria-hidden="true"
            >
              <path
                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            <div class="mt-4">
              <div class="flex text-sm text-gray-600">
                <label
                  for="images"
                  class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                >
                  <span>画像をアップロード</span>
                  <input
                    id="images"
                    name="images"
                    type="file"
                    class="sr-only"
                    multiple
                    accept="image/*"
                    @change="handleImageChange"
                  />
                </label>
                <p class="pl-1">またはドラッグ＆ドロップ</p>
              </div>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
          </div>
        </div>
        <div v-if="form.images.length > 0" class="mt-4 grid grid-cols-2 gap-4">
          <div v-for="(image, index) in form.images" :key="index" class="relative">
            <img :src="image.url" class="w-full h-32 object-cover rounded-lg" />
            <button
              type="button"
              @click="removeImage(index)"
              class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700">ステータス</label>
        <select
          id="status"
          v-model="form.status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
        >
          {{ isEditing ? '更新' : '作成' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import MarkdownEditor from './MarkdownEditor.vue'

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

const emit = defineEmits(['close', 'success', 'update:modelValue', 'cancel']);

const loading = ref(false);
const imageInput = ref(null);

const form = ref({
  title: props.post.title,
  content: props.post.content,
  status: props.post.status,
  scheduled_at: props.post.scheduled_at,
  images: props.post.images || [],
  thumbnail: props.post.thumbnail_url ? {
    url: props.post.thumbnail_url
  } : null,
  is_scheduled: false
});

const isEditing = computed(() => !!props.post.id);

const handleImageChange = (event) => {
  const files = event.target.files;
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        form.value.images.push({
          file,
          url: e.target.result
        });
      };
      reader.readAsDataURL(file);
    }
  }
};

const removeImage = (index) => {
  form.value.images.splice(index, 1);
};

const handleThumbnailChange = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  // ファイル形式のチェック
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!allowedTypes.includes(file.type)) {
    alert('許可されていないファイル形式です。JPEG、PNG、GIF形式の画像を選択してください。');
    event.target.value = '';
    return;
  }

  // ファイルサイズのチェック（2MB）
  const maxSize = 2 * 1024 * 1024;
  if (file.size > maxSize) {
    alert('ファイルサイズが大きすぎます。2MB以下の画像を選択してください。');
    event.target.value = '';
    return;
  }

  console.log('選択されたファイルの詳細:', {
    name: file.name,
    type: file.type,
    size: file.size,
    lastModified: file.lastModified
  });

  const reader = new FileReader();
  reader.onload = (e) => {
    form.value.thumbnail = {
      file,
      url: e.target.result
    };
    console.log('サムネイル設定後:', form.value.thumbnail);
  };
  reader.readAsDataURL(file);
};

const removeThumbnail = () => {
  form.value.thumbnail = null;
};

// フォームの変更を監視（プレビュー用）
watch(
  () => ({
    title: form.value.title,
    content: form.value.content,
    status: form.value.status,
    scheduled_at: form.value.scheduled_at
  }),
  (newValue) => {
    // プレビューの更新のみを行う
    emit('update:modelValue', newValue);
  },
  { deep: true }
);

// フォームの送信処理
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

    // サムネイル画像の処理
    if (form.value.thumbnail?.file) {
      console.log('サムネイルファイルの詳細:', {
        name: form.value.thumbnail.file.name,
        type: form.value.thumbnail.file.type,
        size: form.value.thumbnail.file.size
      });
      // ファイルを直接追加（name属性を追加）
      formData.append('thumbnail', form.value.thumbnail.file, form.value.thumbnail.file.name);
    } else if (form.value.thumbnail?.url) {
      // 既存のサムネイルURLがある場合は追加
      formData.append('thumbnail_path', form.value.thumbnail.url);
    }

    // 画像の処理
    if (form.value.images) {
      form.value.images.forEach((image, index) => {
        if (image.file) {
          formData.append(`images[${index}]`, image.file, image.file.name);
        } else if (typeof image === 'string') {
          formData.append(`existing_images[${index}]`, image);
        }
      });
    }

    // 削除対象の画像IDを追加
    if (form.value.deletedImages) {
      form.value.deletedImages.forEach((id, index) => {
        formData.append(`deleted_images[${index}]`, id);
      });
    }

    // デバッグ用のログ
    console.log('送信するデータ:', {
      title: form.value.title,
      content: form.value.content,
      status: form.value.status,
      scheduled_at: form.value.scheduled_at,
      thumbnail: form.value.thumbnail,
      images: form.value.images,
      deletedImages: form.value.deletedImages
    });

    // FormDataの内容を確認
    console.log('FormDataの内容:');
    for (let [key, value] of formData.entries()) {
      console.log(`${key}:`, value);
    }

    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'multipart/form-data'
    };

    let response;
    if (isEditing.value) {
      // PUTリクエストの場合は_methodパラメータを追加
      formData.append('_method', 'PUT');
      response = await axios.post(`/api/posts/${props.post.id}`, formData, { headers });
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
        const errorMessages = Object.entries(validationErrors)
          .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
          .join('\n');
        alert('入力内容を確認してください。\n\n' + errorMessages);
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

// キャンセル処理
const handleCancel = () => {
  emit('cancel');
  emit('close');
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