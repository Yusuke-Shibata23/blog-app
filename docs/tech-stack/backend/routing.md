# ルーティングシステム

## 概要

本プロジェクトでは、Laravelのルーティングシステムを使用して、APIエンドポイントを定義・管理しています。RESTfulな設計原則に従い、リソースベースのルーティングを実装しています。

## 技術スタック

### 1. Laravel Route System
- RESTful APIルーティング
- ミドルウェアによる認証・認可
- リソースコントローラー
- ルートパラメータバインディング

### 2. 実装例

#### 2.1 基本的なルート定義
```php
// 認証が必要なルートグループ
Route::middleware('auth:sanctum')->group(function () {
    // 投稿関連のルート
    Route::get('/posts/drafts', [PostController::class, 'drafts']);
    Route::get('/posts/scheduled', [PostController::class, 'scheduled']);
    Route::get('/posts/published', [PostController::class, 'published']);
    Route::get('/posts/my', [PostController::class, 'myPosts']);

    // リソースルート
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});
```

#### 2.2 ルートモデルバインディング
```php
// PostController.php
public function show(Post $post)
{
    return new PostResource($post);
}
```

### 3. 主要機能

#### 3.1 ミドルウェア
- 認証ミドルウェア（`auth:sanctum`）
- レート制限ミドルウェア
- CORSミドルウェア

```php
// ミドルウェアの適用例
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // 保護されたルート
});
```

#### 3.2 リソースコントローラー
- 標準的なCRUD操作
- リソースのネスト
- カスタムアクション

```php
// リソースコントローラーの定義
class PostController extends Controller
{
    public function drafts()
    {
        return Post::where('status', 'draft')->get();
    }

    public function scheduled()
    {
        return Post::where('status', 'scheduled')->get();
    }
}
```

### 4. ベストプラクティス

#### 4.1 ルートの整理
- 関連するルートをグループ化
- 適切な命名規則
- 一貫性のあるURL構造

#### 4.2 セキュリティ
- 適切なミドルウェアの適用
- レート制限の設定
- 入力バリデーション

#### 4.3 パフォーマンス
- ルートのキャッシュ
- 適切なミドルウェアの順序
- 不要なミドルウェアの削除

### 5. ルーティングの利点

1. **柔軟性**
   - カスタムルートの定義
   - ミドルウェアの柔軟な適用
   - リソースのネスト

2. **保守性**
   - 明確なルート構造
   - 一貫性のある命名規則
   - ドキュメント化の容易さ

3. **セキュリティ**
   - ミドルウェアによる保護
   - レート制限
   - 入力バリデーション

4. **パフォーマンス**
   - ルートのキャッシュ
   - 効率的なルーティング
   - 最適化されたミドルウェア

### 6. 実装のポイント

#### 6.1 ルートの設計
- RESTful原則に従う
- 適切なHTTPメソッドの使用
- 一貫性のあるURL構造

#### 6.2 エラーハンドリング
- 適切なHTTPステータスコード
- エラーメッセージの標準化
- グローバルなエラーハンドリング

#### 6.3 バージョニング
- APIバージョンの管理
- 後方互換性の維持
- 段階的な移行

### 7. 拡張性

#### 7.1 モジュール化
- 機能ごとのルートグループ
- 再利用可能なミドルウェア
- プラグインシステム

#### 7.2 カスタマイズ
- カスタムルートパラメータ
- カスタムミドルウェア
- カスタムバリデーション

#### 7.3 統合
- サードパーティサービス
- 外部API
- マイクロサービス 
