import './bootstrap';
import '../css/app.css';
import axios from 'axios';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

// axiosのデフォルト設定
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// アプリケーションの作成
const app = createApp(App);

// Piniaの設定
const pinia = createPinia();
app.use(pinia);

// ルーターの設定
app.use(router);

// アプリケーションのマウント
app.mount('#app');
