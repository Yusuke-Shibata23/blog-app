import { defineStore } from 'pinia';
import axios from 'axios';

export const useTagStore = defineStore('tags', {
  state: () => ({
    tags: {}
  }),

  actions: {
    async fetchTags() {
      try {
        const response = await axios.get('/api/tags');
        this.tags = response.data;
      } catch (error) {
        console.error('タグの取得に失敗しました:', error);
        throw error;
      }
    }
  }
}); 