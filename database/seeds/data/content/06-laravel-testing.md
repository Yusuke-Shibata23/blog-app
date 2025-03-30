# Laravelのテスト実践ガイド

## はじめに

Laravelでの効果的なテスト戦略は、アプリケーションの品質を確保するために不可欠です。この記事では、Laravelにおける様々なテスト手法と、実践的なテストの書き方について解説します。

## テストの種類

### 1. ユニットテスト

```php
public function test_user_can_be_created()
{
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com'
    ]);
}
```

### 2. 機能テスト

```php
public function test_user_can_login()
{
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password'
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
}
```

### 3. HTTPテスト

```php
public function test_posts_can_be_retrieved()
{
    $posts = Post::factory()->count(3)->create();

    $response = $this->getJson('/api/posts');

    $response
        ->assertStatus(200)
        ->assertJsonCount(3);
}
```

## テストの設定

### 1. テストデータベース

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_testing
DB_USERNAME=root
DB_PASSWORD=
```

### 2. テスト環境の設定

```php
// phpunit.xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_DATABASE" value="blog_testing"/>
</php>
```

## テストの実践

### 1. モデルのテスト

```php
public function test_post_belongs_to_user()
{
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->assertInstanceOf(User::class, $post->user);
    $this->assertEquals($user->id, $post->user->id);
}
```

### 2. コントローラーのテスト

```php
public function test_post_can_be_created()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/posts', [
        'title' => 'Test Post',
        'content' => 'Test Content'
    ]);

    $response
        ->assertStatus(201)
        ->assertJson([
            'title' => 'Test Post'
        ]);
}
```

### 3. ミドルウェアのテスト

```php
public function test_auth_middleware()
{
    $response = $this->getJson('/api/posts/create');
    $response->assertStatus(401);

    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('/api/posts/create');
    $response->assertStatus(200);
}
```

## テストカバレッジ

1. PHPUnitの設定
2. カバレッジレポートの生成
3. カバレッジ目標の設定

## CI/CDパイプライン

1. GitHubアクションの設定
2. テストの自動実行
3. デプロイ前のテスト確認

## ベストプラクティス

1. テストの命名規則
2. テストデータの準備
3. アサーションの選択
4. テストの独立性確保

## まとめ

効果的なテスト戦略は、アプリケーションの品質と保守性を大きく向上させます。この記事で紹介したテスト手法を実践することで、より信頼性の高いアプリケーションを開発できます。

## 参考リンク

- [Laravel公式テストドキュメント](https://laravel.com/docs/testing)
- [PHPUnit公式ドキュメント](https://phpunit.de/)
- [Laravelテストベストプラクティス](https://example.com/laravel-testing-best-practices) 