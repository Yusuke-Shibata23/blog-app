<template>
  <div class="min-h-screen bg-gray-100">
    <Navigation v-if="isReady" />
    <!-- メインコンテンツ -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <router-view v-if="isReady"></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from './stores/auth'
import Navigation from './components/Navigation.vue'

const auth = useAuth()
const isReady = ref(false)

// アプリケーション起動時に認証状態を確認
onMounted(async () => {
  try {
    await auth.checkAuth()
    isReady.value = true
  } catch (error) {
    console.error('認証状態の確認に失敗しました:', error)
    // エラーが発生しても、アプリケーションは表示する
    isReady.value = true
  }
})
</script> 