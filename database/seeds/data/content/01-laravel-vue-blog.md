# LaravelとVue.jsでブログアプリを作成する方法

## はじめに

LaravelとVue.jsを使用して、モダンなブログアプリケーションを作成する方法について解説します。この記事では、基本的な実装手順から、高度な機能の実装まで、段階的に説明していきます。

![アプリケーションのアーキテクチャ図](/storage/posts/1/architecture.png)
*LaravelとVue.jsを使用したアプリケーションの全体アーキテクチャ*

## 技術スタック

- Laravel 10
- Vue.js 3
- MySQL
- Tailwind CSS

## プロジェクトのセットアップ

### 1. Laravelプロジェクトの作成

```bash
composer create-project laravel/laravel blog-app
cd blog-app
```

![プロジェクトセットアップの手順](/storage/posts/1/setup.png)
*Laravelプロジェクトの初期セットアップ手順*

### 2. 必要なパッケージのインストール

```bash
composer require laravel/sanctum
npm install vue@next @vitejs/plugin-vue
```

### 3. データベースの設定

`.env`ファイルでデータベース接続情報を設定します：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app
DB_USERNAME=root
DB_PASSWORD=
```

## データベース設計

### 1. ユーザーテーブル

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
```

### 2. 投稿テーブル

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->string('status')->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->json('tags')->nullable();
    $table->foreignId('user_id')->constrained();
    $table->timestamps();
});
```

## API実装

### 1. 認証API

```php
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

### 2. 投稿API

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
});
```

## フロントエンド実装

### 1. Vue Routerの設定

```javascript
const routes = [
    {
        path: '/',
        component: Home
    },
    {
        path: '/posts/:id',
        component: PostDetail
    }
];
```

### 2. 状態管理（Pinia）

```javascript
export const usePostStore = defineStore('post', {
    state: () => ({
        posts: [],
        currentPost: null
    }),
    actions: {
        async fetchPosts() {
            // APIから投稿を取得
        }
    }
});
```

## セキュリティ対策

1. CSRF対策
2. XSS対策
3. SQLインジェクション対策
4. ファイルアップロードの制限

## パフォーマンス最適化

1. キャッシュの活用
2. 画像の最適化
3. コード分割
4. レイジーローディング

## テスト

### 1. バックエンドテスト

```php
public function test_can_create_post()
{
    $response = $this->postJson('/api/posts', [
        'title' => 'テスト投稿',
        'content' => 'テスト内容'
    ]);
    
    $response->assertStatus(201);
}
```

### 2. フロントエンドテスト

```javascript
test('投稿一覧が表示される', async () => {
    const wrapper = mount(PostList);
    await wrapper.vm.fetchPosts();
    expect(wrapper.vm.posts.length).toBeGreaterThan(0);
});
```

## デプロイ

1. 本番環境の準備
2. CI/CDパイプラインの構築
3. モニタリングの設定

## まとめ

この記事では、LaravelとVue.jsを使用したブログアプリケーションの基本的な実装手順について説明しました。実際の開発では、要件に応じて機能を追加・カスタマイズしていく必要があります。

## 参考リンク

- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Vue.js公式ドキュメント](https://vuejs.org/)
- [Tailwind CSS公式ドキュメント](https://tailwindcss.com/) 