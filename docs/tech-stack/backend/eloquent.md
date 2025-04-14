# Eloquent ORM

## 概要

LaravelのEloquent ORM（Object-Relational Mapping）は、データベースとの対話をオブジェクト指向で行うための強力なツールです。以下のコード例で使用されている技術について詳しく解説します。

```php
class PostController extends Controller
{
    public function drafts()
    {
        return Post::where('status', 'draft')->get();
    }
}
```

## 使用技術

### 1. Eloquent ORM
- Laravelの標準ORM
- アクティブレコードパターン
- クエリビルダー
- リレーションシップ管理

### 2. 実装の詳細

#### 2.1 モデルの定義
```php
// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'status',
        'user_id'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    // リレーション定義
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### 2.2 クエリビルダー
```php
// where句の使用例
Post::where('status', 'draft')
    ->where('user_id', auth()->id())
    ->orderBy('created_at', 'desc')
    ->get();

// スコープの定義
class Post extends Model
{
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}

// スコープの使用
Post::draft()->get();
```

### 3. 主要機能

#### 3.1 クエリビルダー
- メソッドチェーン
- 条件の組み合わせ
- ソートと制限
- 集計関数

```php
// 複雑なクエリの例
Post::where('status', 'draft')
    ->where(function ($query) {
        $query->where('title', 'like', '%Laravel%')
              ->orWhere('content', 'like', '%Laravel%');
    })
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();
```

#### 3.2 リレーション
- 1対1
- 1対多
- 多対多
- ポリモーフィック

```php
// リレーションの使用例
$post = Post::with('user', 'comments')->find(1);
$post->user->name; // ユーザー名を取得
$post->comments; // コメントを取得
```

### 4. パフォーマンス最適化

#### 4.1 イーガーローディング
```php
// N+1問題の解決
Post::with('user')->get();
```

#### 4.2 クエリの最適化
```php
// 必要なカラムのみを取得
Post::select('id', 'title')->get();

// インデックスの活用
Post::where('status', 'draft')->where('user_id', 1)->get();
```

### 5. セキュリティ

#### 5.1 マスアサインメント
```php
// $fillableプロパティによる保護
protected $fillable = ['title', 'content', 'status'];

// マスアサインメントの使用
Post::create($request->validated());
```

#### 5.2 クエリインジェクション対策
- プリペアドステートメントの自動使用
- パラメータバインディング
- エスケープ処理

### 6. 拡張機能

#### 6.1 アクセサとミューテタ
```php
class Post extends Model
{
    // アクセサ
    public function getTitleAttribute($value)
    {
        return ucfirst($value);
    }

    // ミューテタ
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strtolower($value);
    }
}
```

#### 6.2 イベント
```php
class Post extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
    }
}
```

### 7. ベストプラクティス

#### 7.1 クエリの最適化
- 必要なカラムのみを選択
- インデックスの活用
- イーガーローディングの使用

#### 7.2 コードの整理
- スコープの活用
- リポジトリパターンの使用
- サービスクラスの活用

#### 7.3 パフォーマンス
- クエリのキャッシュ
- バッチ処理の使用
- チャンク処理の活用 
