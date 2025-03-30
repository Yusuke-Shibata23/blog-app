import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    isInitialized: false
  }),

  getters: {
    isAuthenticated: (state) => {
      return !!state.token && !!state.user
    }
  },

  actions: {
    setAuthHeader() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      } else {
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async login(credentials) {
      try {
        const response = await axios.post('/api/login', {
          ...credentials,
          remember: true
        })
        const { token, user } = response.data
        
        this.token = token
        this.user = user
        this.isInitialized = true
        localStorage.setItem('token', token)
        this.setAuthHeader()
        
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
        this.isInitialized = true
        localStorage.setItem('token', token)
        this.setAuthHeader()
        
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
        this.isInitialized = false
        localStorage.removeItem('token')
        this.setAuthHeader()
      } catch (error) {
        console.error('ログアウトに失敗しました:', error)
        // エラーが発生しても、ローカルの状態はクリアする
        this.token = null
        this.user = null
        this.isInitialized = false
        localStorage.removeItem('token')
        this.setAuthHeader()
        throw error
      }
    },

    async checkAuth() {
      if (!this.token) {
        console.log('トークンが存在しません')
        this.user = null
        this.isInitialized = true
        this.setAuthHeader()
        return false
      }

      try {
        console.log('認証状態の確認を開始します')
        // まず認証ヘッダーを設定
        this.setAuthHeader()

        // ユーザー情報を取得
        const response = await axios.get('/api/user', {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          validateStatus: function (status) {
            return status >= 200 && status < 500 // 500未満のステータスコードを許可
          }
        })

        console.log('ユーザー情報のレスポンス:', response.data)

        if (response.data && response.data.id) {
          console.log('ユーザー情報の取得に成功しました:', response.data.name)
          this.user = response.data
          this.isInitialized = true
          return true
        } else {
          console.error('ユーザー情報が不正な形式です:', response.data)
          this.token = null
          this.user = null
          this.isInitialized = true
          localStorage.removeItem('token')
          this.setAuthHeader()
          return false
        }
      } catch (error) {
        console.error('認証状態の確認に失敗しました:', {
          message: error.message,
          status: error.response?.status,
          data: error.response?.data,
          headers: error.response?.headers,
          stack: error.stack
        })

        if (error.response?.status === 401) {
          console.log('認証エラー（401）が発生しました')
          this.token = null
          this.user = null
          this.isInitialized = true
          localStorage.removeItem('token')
          this.setAuthHeader()
        } else if (error.response?.status === 500) {
          console.error('サーバーエラー（500）が発生しました')
        } else if (error.response?.status === 404) {
          console.error('エンドポイントが見つかりません（404）')
        } else if (error.response?.status === 419) {
          console.error('CSRFトークンの有効期限切れです（419）')
        } else {
          console.error('予期せぬエラーが発生しました:', error)
        }
        return false
      }
    }
  }
})