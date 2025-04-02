# 共通コンポーネント

## Button.vue

### 概要
汎用的なボタンコンポーネント。様々なスタイルと状態をサポート。

### プロパティ
```typescript
interface Props {
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'secondary' | 'danger' | 'success' | 'warning';
    size?: 'sm' | 'md' | 'lg';
    disabled?: boolean;
    loading?: boolean;
    block?: boolean;
    icon?: string;
}
```

### スロット
```typescript
interface Slots {
    default: () => VNode;      // ボタンテキスト
    icon?: () => VNode;        // アイコン
}
```

### スタイル
```css
.button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    font-weight: 500;
    transition: all 0.2s;
    
    &--primary {
        background-color: var(--color-primary);
        color: white;
        
        &:hover {
            background-color: var(--color-primary-dark);
        }
    }
    
    &--secondary {
        background-color: var(--color-secondary);
        color: white;
        
        &:hover {
            background-color: var(--color-secondary-dark);
        }
    }
    
    &--disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    &--loading {
        position: relative;
        color: transparent;
        
        &::after {
            content: '';
            position: absolute;
            width: 1rem;
            height: 1rem;
            border: 2px solid currentColor;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 0.8s linear infinite;
        }
    }
}
```

## Input.vue

### 概要
フォーム入力フィールドコンポーネント。バリデーションとエラー表示をサポート。

### プロパティ
```typescript
interface Props {
    type?: string;
    modelValue: string | number;
    label?: string;
    placeholder?: string;
    error?: string;
    required?: boolean;
    disabled?: boolean;
    readonly?: boolean;
    autocomplete?: string;
}
```

### イベント
```typescript
interface Emits {
    (e: 'update:modelValue', value: string | number): void;
    (e: 'blur'): void;
    (e: 'focus'): void;
}
```

### スタイル
```css
.input {
    position: relative;
    margin-bottom: 1rem;
    
    &__label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        
        &--required::after {
            content: '*';
            color: var(--color-danger);
            margin-left: 0.25rem;
        }
    }
    
    &__field {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid var(--color-border);
        border-radius: 0.25rem;
        transition: border-color 0.2s;
        
        &:focus {
            outline: none;
            border-color: var(--color-primary);
        }
        
        &--error {
            border-color: var(--color-danger);
        }
        
        &--disabled {
            background-color: var(--color-background-light);
            cursor: not-allowed;
        }
    }
    
    &__error {
        color: var(--color-danger);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
}
```

## Card.vue

### 概要
カードコンポーネント。コンテンツをカード形式で表示。

### プロパティ
```typescript
interface Props {
    title?: string;
    subtitle?: string;
    padding?: string;
    shadow?: boolean;
    hover?: boolean;
}
```

### スロット
```typescript
interface Slots {
    default: () => VNode;      // メインコンテンツ
    header?: () => VNode;      // ヘッダー
    footer?: () => VNode;      // フッター
}
```

### スタイル
```css
.card {
    background-color: white;
    border-radius: 0.5rem;
    overflow: hidden;
    
    &--shadow {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    &--hover {
        transition: transform 0.2s, box-shadow 0.2s;
        
        &:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    }
    
    &__header {
        padding: 1rem;
        border-bottom: 1px solid var(--color-border);
    }
    
    &__body {
        padding: 1rem;
    }
    
    &__footer {
        padding: 1rem;
        border-top: 1px solid var(--color-border);
    }
}
```

## Modal.vue

### 概要
モーダルダイアログコンポーネント。ポップアップ表示を提供。

### プロパティ
```typescript
interface Props {
    show: boolean;
    title?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl';
    closeOnBackdrop?: boolean;
    closeOnEscape?: boolean;
}
```

### イベント
```typescript
interface Emits {
    (e: 'update:show', value: boolean): void;
    (e: 'close'): void;
}
```

### スロット
```typescript
interface Slots {
    default: () => VNode;      // モーダルコンテンツ
    header?: () => VNode;      // ヘッダー
    footer?: () => VNode;      // フッター
}
```

### スタイル
```css
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    
    &__dialog {
        background-color: white;
        border-radius: 0.5rem;
        max-width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        
        &--sm {
            width: 300px;
        }
        
        &--md {
            width: 500px;
        }
        
        &--lg {
            width: 800px;
        }
        
        &--xl {
            width: 1140px;
        }
    }
    
    &__header {
        padding: 1rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    &__body {
        padding: 1rem;
    }
    
    &__footer {
        padding: 1rem;
        border-top: 1px solid var(--color-border);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    
    &__close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.25rem;
        color: var(--color-text);
        
        &:hover {
            color: var(--color-primary);
        }
    }
}
```

## Pagination.vue

### 概要
ページネーションコンポーネント。ページ切り替えを提供。

### プロパティ
```typescript
interface Props {
    currentPage: number;
    totalPages: number;
    maxVisiblePages?: number;
    showFirstLast?: boolean;
    showPrevNext?: boolean;
}
```

### イベント
```typescript
interface Emits {
    (e: 'update:currentPage', page: number): void;
}
```

### スタイル
```css
.pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    
    &__item {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.25rem;
        background-color: white;
        border: 1px solid var(--color-border);
        cursor: pointer;
        transition: all 0.2s;
        
        &:hover {
            background-color: var(--color-background-light);
        }
        
        &--active {
            background-color: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        &--disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    }
}
```

## Loading.vue

### 概要
ローディング表示コンポーネント。読み込み中の状態を表示。

### プロパティ
```typescript
interface Props {
    size?: 'sm' | 'md' | 'lg';
    color?: string;
    overlay?: boolean;
    text?: string;
}
```

### スタイル
```css
.loading {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    
    &--overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 1000;
    }
    
    &__spinner {
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid currentColor;
        border-radius: 50%;
        border-right-color: transparent;
        animation: spin 0.8s linear infinite;
        
        &--sm {
            width: 1rem;
            height: 1rem;
        }
        
        &--lg {
            width: 2rem;
            height: 2rem;
        }
    }
    
    &__text {
        font-size: 0.875rem;
        color: currentColor;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
``` 