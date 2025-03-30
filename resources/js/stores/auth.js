import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token
  },

  actions: {
    async login(credentials) {
      try {
        const response = await axios.post('/api/login', credentials)
        const { token, user } = response.data
        
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        
        // 認証トークンをaxiosのデフォルトヘッダーに設定
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return response.data
      } catch (error) {
        throw error
      }
    },

    async register(userData) {
      try {
        const response = await axios.post('/api/register', userData)
        const { token, user } = response.data
        
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        
        // 認証トークンをaxiosのデフォルトヘッダーに設定
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return response.data
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      } catch (error) {
        console.error('ログアウトに失敗しました:', error)
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/api/user')
        this.user = response.data
      } catch (error) {
        console.error('ユーザー情報の取得に失敗しました:', error)
      }
    }
  }
}) 