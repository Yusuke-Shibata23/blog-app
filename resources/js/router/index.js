import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@/stores/auth';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/Register.vue')
  },
  {
    path: '/posts',
    name: 'posts',
    component: () => import('../views/Posts.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/posts/create',
    name: 'post-create',
    component: () => import('../views/PostCreate.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/posts/:id',
    name: 'post-detail',
    component: () => import('../views/PostDetail.vue')
  },
  {
    path: '/posts/public/:id',
    name: 'post-detail-public',
    component: () => import('../views/PostDetail.vue')
  }
];

// ルーターの作成
const router = createRouter({
  history: createWebHistory(),
  routes
});

// ナビゲーションガード
router.beforeEach(async (to, from, next) => {
  const auth = useAuth();
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    await auth.checkAuth();
    if (!auth.isAuthenticated) {
      next('/login');
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router; 