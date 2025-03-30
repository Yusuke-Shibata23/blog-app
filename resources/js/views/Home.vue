<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">ブログ一覧</h1>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <!-- 仮のブログ投稿データ -->
      <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">サンプルブログ投稿 {{ i }}</h2>
        <p class="text-gray-600 mb-4">これはサンプルのブログ投稿です。実際のデータは後で実装します。</p>
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-500">2024-03-30</span>
          <router-link
            :to="'/posts/' + i"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            続きを読む
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// 後で実際のデータを取得する処理を実装
</script> 