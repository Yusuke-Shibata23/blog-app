import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@/stores/auth';
import Home from '@/views/Home.vue';
import Login from '@/views/Login.vue';
import Register from '@/views/Register.vue';
import Posts from '@/views/Posts.vue';

// ルートの定義
const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
  },
  {
    path: '/posts',
    name: 'posts',
    component: Posts,
    meta: {
      requiresAuth: false // 未認証ユーザーも閲覧可能
    }
  },
];

// ルーターの作成
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// ナビゲーションガード
router.beforeEach((to, from, next) => {
  const auth = useAuth();
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router; 