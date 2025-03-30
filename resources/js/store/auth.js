import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        isAuthenticated: false,
    }),

    getters: {
        getUser: (state) => state.user,
        getToken: (state) => state.token,
        isLoggedIn: (state) => state.isAuthenticated,
    },

    actions: {
        async register(credentials) {
            try {
                const response = await axios.post('/api/register', credentials);
                this.setUser(response.data.user);
                this.setToken(response.data.token);
                return response;
            } catch (error) {
                throw error;
            }
        },

        async login(credentials) {
            try {
                const response = await axios.post('/api/login', credentials);
                this.setUser(response.data.user);
                this.setToken(response.data.token);
                return response;
            } catch (error) {
                throw error;
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
                this.clearAuth();
            } catch (error) {
                throw error;
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.setUser(response.data);
            } catch (error) {
                this.clearAuth();
                throw error;
            }
        },

        setUser(user) {
            this.user = user;
            this.isAuthenticated = true;
        },

        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            this.isAuthenticated = false;
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        },

        initializeAuth() {
            const token = localStorage.getItem('token');
            if (token) {
                this.setToken(token);
                this.fetchUser();
            }
        },
    },
}); 