<template>
  <div class="min-h-screen bg-gray-100">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="text-xl font-bold text-gray-800">
                Blog App
              </router-link>
            </div>
          </div>
          <div class="flex items-center">
            <router-link
              v-if="!isAuthenticated"
              to="/login"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              ログイン
            </router-link>
            <router-link
              v-if="!isAuthenticated"
              to="/register"
              class="ml-4 bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
            >
              新規登録
            </router-link>
            <button
              v-if="isAuthenticated"
              @click="logout"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              ログアウト
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- メインコンテンツ -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isAuthenticated = ref(false);

const logout = async () => {
  // TODO: ログアウト処理の実装
  isAuthenticated.value = false;
  router.push('/login');
};
</script> 