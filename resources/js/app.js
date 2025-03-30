import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

// アプリケーションの作成
const app = createApp(App);

// Piniaの設定
const pinia = createPinia();
app.use(pinia);

// ルーターの設定
app.use(router);

// アプリケーションのマウント
app.mount('#app');
