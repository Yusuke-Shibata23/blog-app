# レイアウトコンポーネント

## AppLayout.vue

### 概要
アプリケーションのメインレイアウトコンポーネント。ヘッダー、メインコンテンツ、フッターの基本構造を提供。

### プロパティ
```typescript
interface Props {
    showHeader?: boolean;      // ヘッダー表示の有無
    showFooter?: boolean;      // フッター表示の有無
    fixedHeader?: boolean;     // ヘッダーを固定表示
    container?: boolean;       // コンテンツを中央寄せ
}
```

### スロット
```typescript
interface Slots {
    default: () => VNode;      // メインコンテンツ
    header?: () => VNode;      // ヘッダー
    footer?: () => VNode;      // フッター
    sidebar?: () => VNode;     // サイドバー
}
```

### スタイル
```css
.app-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: var(--color-background);
}

.app-layout__header {
    position: relative;
    z-index: 100;
    background-color: white;
    border-bottom: 1px solid var(--color-border);
    
    &--fixed {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
    }
}

.app-layout__main {
    flex: 1;
    display: flex;
    position: relative;
    
    &--container {
        max-width: var(--container-width);
        margin: 0 auto;
        padding: 0 1rem;
    }
}

.app-layout__sidebar {
    width: var(--sidebar-width);
    background-color: white;
    border-right: 1px solid var(--color-border);
    position: fixed;
    top: var(--header-height);
    bottom: 0;
    left: 0;
    overflow-y: auto;
}

.app-layout__content {
    flex: 1;
    padding: 2rem;
    margin-left: var(--sidebar-width);
}

.app-layout__footer {
    background-color: white;
    border-top: 1px solid var(--color-border);
    padding: 2rem 0;
}
```

## AuthLayout.vue

### 概要
認証関連ページのレイアウトコンポーネント。ログイン、登録ページの共通レイアウトを提供。

### プロパティ
```typescript
interface Props {
    title?: string;            // ページタイトル
    subtitle?: string;         // サブタイトル
    showLogo?: boolean;        // ロゴ表示の有無
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
.auth-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: var(--color-background);
}

.auth-layout__header {
    padding: 2rem;
    text-align: center;
}

.auth-layout__logo {
    margin-bottom: 1rem;
    width: 120px;
    height: auto;
}

.auth-layout__title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.auth-layout__subtitle {
    color: var(--color-text-secondary);
    font-size: 1rem;
}

.auth-layout__main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.auth-layout__card {
    width: 100%;
    max-width: 400px;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.auth-layout__footer {
    padding: 1rem;
    text-align: center;
    color: var(--color-text-secondary);
    font-size: 0.875rem;
}
```

## AdminLayout.vue

### 概要
管理画面用のレイアウトコンポーネント。サイドバーナビゲーションとヘッダーを提供。

### プロパティ
```typescript
interface Props {
    showSidebar?: boolean;     // サイドバー表示の有無
    sidebarCollapsed?: boolean; // サイドバーを折りたたむ
    showBreadcrumb?: boolean;  // パンくずリスト表示の有無
}
```

### スロット
```typescript
interface Slots {
    default: () => VNode;      // メインコンテンツ
    header?: () => VNode;      // ヘッダー
    sidebar?: () => VNode;     // サイドバー
    breadcrumb?: () => VNode;  // パンくずリスト
}
```

### スタイル
```css
.admin-layout {
    min-height: 100vh;
    display: flex;
    background-color: var(--color-background);
}

.admin-layout__sidebar {
    width: var(--sidebar-width);
    background-color: white;
    border-right: 1px solid var(--color-border);
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    overflow-y: auto;
    transition: width 0.3s;
    
    &--collapsed {
        width: var(--sidebar-collapsed-width);
    }
}

.admin-layout__main {
    flex: 1;
    margin-left: var(--sidebar-width);
    transition: margin-left 0.3s;
    
    &--sidebar-collapsed {
        margin-left: var(--sidebar-collapsed-width);
    }
}

.admin-layout__header {
    position: sticky;
    top: 0;
    z-index: 100;
    background-color: white;
    border-bottom: 1px solid var(--color-border);
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.admin-layout__content {
    padding: 2rem;
}

.admin-layout__breadcrumb {
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--color-border);
}

.admin-layout__toggle-sidebar {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    color: var(--color-text);
    
    &:hover {
        color: var(--color-primary);
    }
}
```

## レスポンシブ対応

### ブレークポイント
```css
:root {
    --breakpoint-sm: 640px;
    --breakpoint-md: 768px;
    --breakpoint-lg: 1024px;
    --breakpoint-xl: 1280px;
}
```

### メディアクエリ
```css
@media (max-width: var(--breakpoint-lg)) {
    .admin-layout__sidebar {
        transform: translateX(-100%);
        position: fixed;
        z-index: 1000;
        
        &--show {
            transform: translateX(0);
        }
    }
    
    .admin-layout__main {
        margin-left: 0;
    }
}

@media (max-width: var(--breakpoint-md)) {
    .auth-layout__card {
        padding: 1.5rem;
    }
    
    .app-layout__content {
        padding: 1rem;
    }
}
```

## テーマ設定

### カラーパレット
```css
:root {
    --color-primary: #3b82f6;
    --color-primary-dark: #2563eb;
    --color-secondary: #6b7280;
    --color-secondary-dark: #4b5563;
    --color-success: #10b981;
    --color-danger: #ef4444;
    --color-warning: #f59e0b;
    --color-info: #3b82f6;
    
    --color-background: #f3f4f6;
    --color-background-light: #f9fafb;
    --color-text: #1f2937;
    --color-text-secondary: #6b7280;
    --color-border: #e5e7eb;
}
```

### レイアウト変数
```css
:root {
    --header-height: 64px;
    --sidebar-width: 240px;
    --sidebar-collapsed-width: 64px;
    --container-width: 1200px;
    --content-padding: 2rem;
}
``` 