<template>
  <div class="tag-selector">
    <label class="block text-sm font-medium text-gray-700 mb-2">タグ</label>
    <div class="flex flex-wrap gap-2 mb-2">
      <button
        v-for="(name, id) in tags"
        :key="id"
        type="button"
        @click="toggleTag(Number(id))"
        :class="[
          'px-3 py-1 rounded-full text-sm font-medium transition-colors',
          modelValue.includes(Number(id))
            ? 'bg-blue-500 text-white hover:bg-blue-600'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
      >
        {{ name }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { tags } from '@/config/tags';

const props = defineProps({
  modelValue: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['update:modelValue']);

const toggleTag = (tagId) => {
  const newTags = [...props.modelValue];
  const index = newTags.indexOf(tagId);
  
  if (index === -1) {
    newTags.push(tagId);
  } else {
    newTags.splice(index, 1);
  }
  
  emit('update:modelValue', newTags);
};
</script> 