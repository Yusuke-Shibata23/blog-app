<template>
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <router-link to="/" class="text-xl font-bold text-gray-800">
              ブログアプリ
            </router-link>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              to="/"
              class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900"
              :class="{ 'border-b-2 border-blue-500': $route.path === '/' }"
            >
              ホーム
            </router-link>
            <router-link
              to="/posts"
              class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900"
              :class="{ 'border-b-2 border-blue-500': $route.path === '/posts' }"
            >
              投稿一覧
            </router-link>
          </div>
        </div>
        <div class="flex items-center">
          <template v-if="auth.isAuthenticated">
            <span class="text-sm text-gray-700 mr-4">
              {{ auth.user?.name }}さん
            </span>
            <button
              @click="handleLogout"
              class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700"
            >
              ログアウト
            </button>
          </template>
          <template v-else>
            <router-link
              to="/login"
              class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              ログイン
            </router-link>
            <router-link
              to="/register"
              class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
            >
              新規登録
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  try {
    await auth.logout()
    router.push('/login')
  } catch (error) {
    console.error('ログアウトに失敗しました:', error)
    alert('ログアウトに失敗しました。')
  }
}
</script> 