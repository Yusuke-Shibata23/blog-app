# コントローラー実装の解説

## はじめに

この文書では、Laravelのコントローラー実装について、具体的なコード例を交えながら解説します。特に、以下のような実装パターンに焦点を当てます：

```php
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

## このコードは何をしているのか？

このコードは、ブログ投稿の「下書き」と「予約投稿」を取得するためのメソッドを実装しています。具体的には：

1. `drafts()`メソッド：
   - データベースから「status」が「draft」の投稿を取得
   - 取得したデータをそのまま返却

2. `scheduled()`メソッド：
   - データベースから「status」が「scheduled」の投稿を取得
   - 取得したデータをそのまま返却

## 技術的な解説

### 1. コントローラーの基本構造

Laravelのコントローラーは、HTTPリクエストを処理するためのクラスです。この例では：

- `PostController`という名前のクラスを作成（カスタム実装）
- `Controller`クラスを継承（Laravel標準機能）
- 各メソッドが特定のエンドポイントに対応（カスタム実装）

### 2. データベース操作

`Post::where('status', 'draft')->get()`というコードは：

1. `Post`モデルを使用してデータベースにアクセス（LaravelのEloquent ORM）
2. `where`句で条件を指定（Laravelのクエリビルダー）
3. `get()`メソッドで条件に合致する全てのレコードを取得（Laravelのクエリビルダー）

これはLaravelのEloquent ORMという機能を使用しており、SQLを直接書かずにデータベース操作が可能です。

### 3. レスポンスの処理

このコードでは、取得したデータをそのまま返却していますが、実際のアプリケーションでは：

- データの整形（カスタム実装）
- エラーハンドリング（Laravelの例外処理）
- 認証・認可のチェック（Laravel Sanctum）
- ページネーション（Laravelのページネーション機能）

などの処理を追加することが一般的です。

## より良い実装にするには？

### 1. 共通ロジックの抽出

同じようなクエリを複数のメソッドで使用する場合、以下のように整理できます：

```php
class PostController extends Controller
{
    public function drafts()
    {
        return $this->getPostsByStatus('draft');
    }

    public function scheduled()
    {
        return $this->getPostsByStatus('scheduled');
    }

    private function getPostsByStatus($status)
    {
        return Post::where('status', $status)
            ->with('user')  // Laravelのリレーション機能
            ->orderBy('created_at', 'desc')  // Laravelのクエリビルダー
            ->get();
    }
}
```

### 2. エラーハンドリングの追加

データベース操作は失敗する可能性があるため、エラーハンドリングを追加します：

```php
public function drafts()
{
    try {
        return Post::where('status', 'draft')->get();
    } catch (\Exception $e) {
        return response()->json([  // Laravelのレスポンス機能
            'message' => '投稿の取得に失敗しました。'  // カスタムメッセージ
        ], 500);
    }
}
```

### 3. 認証の追加

ユーザーごとにデータを制限する場合：

```php
public function drafts()
{
    return Post::where('status', 'draft')
        ->where('user_id', auth()->id())  // Laravelの認証機能
        ->get();
}
```

### 4. パフォーマンスの最適化

大量のデータを扱う場合の最適化：

```php
public function drafts()
{
    return Post::where('status', 'draft')
        ->select('id', 'title', 'created_at')  // Laravelのクエリビルダー
        ->with(['user' => function ($query) {  // Laravelのリレーション機能
            $query->select('id', 'name');
        }])
        ->paginate(15);  // Laravelのページネーション機能
}
```

## 実際の使用例

このコントローラーは、以下のような場面で使用されます：

1. ユーザーが下書き一覧を表示する時（カスタム実装）
2. 予約投稿の管理画面を表示する時（カスタム実装）
3. APIエンドポイントとして他のアプリケーションからデータを取得する時（LaravelのAPI機能）

## まとめ

この実装パターンは：

- シンプルで理解しやすい（カスタム実装）
- 拡張性が高い（Laravelの設計思想）
- Laravelの機能を効果的に活用（Laravel標準機能）

しています。必要に応じて、セキュリティ、パフォーマンス、エラーハンドリングなどの機能を追加することで、より堅牢な実装にすることができます。

## 使用している主なLaravel機能

1. **Eloquent ORM**
   - データベース操作
   - リレーション管理
   - クエリビルダー

2. **認証・認可**
   - SanctumによるAPI認証
   - ユーザー認証
   - ミドルウェア

3. **レスポンス処理**
   - JSONレスポンス
   - ページネーション
   - リソース変換

4. **エラーハンドリング**
   - 例外処理
   - ログ機能
   - デバッグツール

## カスタム実装部分

1. **ビジネスロジック**
   - 投稿の状態管理
   - ユーザー固有のデータ処理
   - アプリケーション固有のルール

2. **コントローラー構造**
   - メソッドの整理
   - 共通ロジックの抽出
   - エラーメッセージのカスタマイズ

3. **API設計**
   - エンドポイントの設計