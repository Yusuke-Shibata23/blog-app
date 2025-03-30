<template>
  <div class="min-h-screen bg-gray-100">
    <Navigation />
    <!-- メインコンテンツ -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import Navigation from '@/components/Navigation.vue'

const auth = useAuthStore()

// アプリケーション起動時に認証状態を確認
onMounted(async () => {
  await auth.checkAuth()
})
</script> 