<!-- MarkdownEditor.vue -->
<template>
  <div class="markdown-editor">
    <!-- ツールバー -->
    <div class="toolbar mb-4 flex flex-wrap gap-2 p-2 bg-gray-100 rounded-lg">
      <button
        v-for="(tool, index) in toolbarTools"
        :key="index"
        type="button"
        @click="insertMarkdown(tool.insert)"
        class="px-3 py-1 text-sm bg-white rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
        :title="tool.title"
      >
        {{ tool.label }}
      </button>
    </div>

    <!-- エディターとプレビューのコンテナ -->
    <div class="editor-container flex gap-4">
      <!-- エディター -->
      <div class="editor flex-1">
        <textarea
          ref="editor"
          v-model="content"
          @input="updatePreview"
          class="w-full h-[500px] p-4 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          placeholder="Markdownで記述してください..."
        ></textarea>
      </div>

      <!-- プレビュー -->
      <div class="preview flex-1 p-4 border rounded-lg overflow-auto h-[500px]">
        <div v-html="previewContent" class="prose max-w-none"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { marked } from 'marked'
import hljs from 'highlight.js'
import 'highlight.js/styles/github.css'
import { useDebounceFn } from '@vueuse/core'

// Props
const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

// Emits
const emit = defineEmits(['update:modelValue'])

// Refs
const editor = ref(null)
const content = ref(props.modelValue)
const previewContent = ref('')

// ツールバーの設定
const toolbarTools = [
  { label: '見出し1', title: '見出し1を挿入', insert: '# ' },
  { label: '見出し2', title: '見出し2を挿入', insert: '## ' },
  { label: '見出し3', title: '見出し3を挿入', insert: '### ' },
  { label: '太字', title: '太字を挿入', insert: '**太字**' },
  { label: 'イタリック', title: 'イタリックを挿入', insert: '*イタリック*' },
  { label: 'リスト', title: 'リストを挿入', insert: '- ' },
  { label: '番号付きリスト', title: '番号付きリストを挿入', insert: '1. ' },
  { label: '引用', title: '引用を挿入', insert: '> ' },
  { label: 'コード', title: 'コードブロックを挿入', insert: '```\nコード\n```' },
  { label: 'リンク', title: 'リンクを挿入', insert: '[リンクテキスト](URL)' },
  { label: '画像', title: '画像を挿入', insert: '![代替テキスト](画像URL)' }
]

// markedの設定
marked.setOptions({
  highlight: function(code, lang) {
    if (lang && hljs.getLanguage(lang)) {
      return hljs.highlight(code, { language: lang }).value
    }
    return hljs.highlightAuto(code).value
  },
  breaks: true
})

// プレビューの更新（デバウンス付き）
const updatePreview = useDebounceFn(() => {
  previewContent.value = marked(content.value)
}, 300)

// マークダウンの挿入
const insertMarkdown = (text) => {
  const textarea = editor.value
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = content.value.substring(start, end)
  
  let newText = text
  if (selectedText) {
    // テキストが選択されている場合、そのテキストをマークダウンで囲む
    if (text.startsWith('**') || text.startsWith('*')) {
      newText = text.replace(/\*/g, '') + selectedText + text.replace(/\*/g, '')
    } else if (text.startsWith('>')) {
      newText = text + selectedText
    } else {
      newText = text + selectedText
    }
  }
  
  content.value = content.value.substring(0, start) + newText + content.value.substring(end)
  
  // カーソル位置の更新
  const newCursorPos = start + newText.length
  nextTick(() => {
    textarea.focus()
    textarea.setSelectionRange(newCursorPos, newCursorPos)
  })
}

// モデル値の監視
watch(() => props.modelValue, (newValue) => {
  if (newValue !== content.value) {
    content.value = newValue
    updatePreview()
  }
})

// コンテンツの変更を親コンポーネントに通知
watch(content, (newValue) => {
  emit('update:modelValue', newValue)
})

// 初期プレビューの生成
onMounted(() => {
  updatePreview()
})
</script>

<style>
.markdown-editor {
  width: 100%;
}

.editor textarea {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.preview {
  background-color: #ffffff;
}

/* プレビューのスタイル */
.prose {
  color: #1f2937;
}

.prose h1 {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.prose h2 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.prose h3 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.prose p {
  margin-bottom: 1rem;
}

.prose ul {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.prose ol {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.prose blockquote {
  border-left: 4px solid #d1d5db;
  padding-left: 1rem;
  font-style: italic;
  margin: 1rem 0;
}

.prose code {
  background-color: #f3f4f6;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
}

.prose pre {
  background-color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin-bottom: 1rem;
}

.prose img {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  margin: 1rem 0;
}

.prose a {
  color: #2563eb;
}

.prose a:hover {
  text-decoration: underline;
}
</style> 