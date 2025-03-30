import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuth = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    loading: false
  }),

  getters: {
    currentUser: (state) => state.user,
    isLoggedIn: (state) => state.isAuthenticated
  },

  actions: {
    async login(credentials) {
      try {
        this.loading = true;
        const response = await axios.post('/api/login', credentials);
        const token = response.data.token;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await this.checkAuth();
        return response.data;
      } catch (error) {
        console.error('ログインエラー:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async register(userData) {
      try {
        this.loading = true;
        const response = await axios.post('/api/register', userData);
        const token = response.data.token;
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await this.checkAuth();
        return response.data;
      } catch (error) {
        console.error('登録エラー:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      try {
        this.loading = true;
        await axios.post('/api/logout');
        this.user = null;
        this.isAuthenticated = false;
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
      } catch (error) {
        console.error('ログアウトエラー:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async checkAuth() {
      try {
        console.log('トークンが存在するため、認証状態を確認します');
        const token = localStorage.getItem('token');
        if (!token) {
          console.log('トークンが存在しないため、未認証状態とします');
          this.user = null;
          this.isAuthenticated = false;
          return;
        }

        console.log('認証状態の確認を開始します');
        const response = await axios.get('/api/user');
        console.log('ユーザー情報のレスポンス:', response.data);

        if (response.data && response.data.id) {
          this.user = response.data;
          this.isAuthenticated = true;
          console.log('ユーザー情報の取得に成功しました:', this.user.name);
        } else {
          console.error('ユーザー情報が不正な形式です');
          this.user = null;
          this.isAuthenticated = false;
        }
      } catch (error) {
        console.error('認証状態の確認に失敗しました:', error);
        this.user = null;
        this.isAuthenticated = false;
      }
    }
  }
})