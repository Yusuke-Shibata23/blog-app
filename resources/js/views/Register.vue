<template>
  <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">新規登録</h2>
    <form @submit.prevent="handleRegister" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">名前</label>
        <input
          type="text"
          id="name"
          v-model="name"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
        <input
          type="email"
          id="email"
          v-model="email"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
        <input
          type="password"
          id="password"
          v-model="password"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">パスワード（確認）</label>
        <input
          type="password"
          id="password_confirmation"
          v-model="passwordConfirmation"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      <div v-if="error" class="text-red-600 text-sm">
        {{ error }}
      </div>
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
      >
        {{ loading ? '登録中...' : '登録' }}
      </button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-600">
      すでにアカウントをお持ちの方は
      <router-link to="/login" class="text-blue-600 hover:text-blue-800">
        ログイン
      </router-link>
      へ
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';

const router = useRouter();
const authStore = useAuthStore();
const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const error = ref('');
const loading = ref(false);

const handleRegister = async () => {
  try {
    loading.value = true;
    error.value = '';
    await authStore.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });
    router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || '登録に失敗しました。';
  } finally {
    loading.value = false;
  }
};
</script> 