<template>
  <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">ログイン</h2>
    <form @submit.prevent="handleLogin" class="space-y-4">
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
      <div v-if="error" class="text-red-600 text-sm">
        {{ error }}
      </div>
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
      >
        {{ loading ? 'ログイン中...' : 'ログイン' }}
      </button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-600">
      アカウントをお持ちでない方は
      <router-link to="/register" class="text-blue-600 hover:text-blue-800">
        新規登録
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
const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  try {
    loading.value = true;
    error.value = '';
    await authStore.login({
      email: email.value,
      password: password.value,
    });
    router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || 'ログインに失敗しました。';
  } finally {
    loading.value = false;
  }
};
</script> 