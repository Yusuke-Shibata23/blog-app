# 認証システム

## 概要

本プロジェクトでは、Laravel Sanctumを使用したAPI認証システムを実装しています。SPA（Single Page Application）とモバイルアプリケーションの両方に対応した認証システムを提供します。

## 技術スタック

### Laravel Sanctum
- APIトークンベースの認証
- SPA認証（Cookieベース）
- スコープベースの権限管理
- トークンの有効期限管理

### 実装の詳細

#### 1. 認証ルート
```php
// 認証関連のルート
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 認証が必要なルート
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
```

#### 2. 認証フロー

##### ユーザー登録
1. クライアントからユーザー情報を送信
2. バリデーション
3. ユーザー作成
4. トークン生成
5. レスポンス返却

##### ログイン
1. クライアントから認証情報を送信
2. 認証情報の検証
3. トークン生成
4. レスポンス返却

##### ログアウト
1. トークンの無効化
2. セッションのクリア

#### 3. セキュリティ対策

##### CSRF保護
- SanctumのCSRFトークン検証
- セッションベースのCSRF保護

##### トークン管理
- トークンの有効期限設定
- トークンのスコープ管理
- トークンの無効化機能

##### レート制限
- ログイン試行回数の制限
- APIリクエストの制限

## 使用方法

### 1. ユーザー登録
```javascript
// フロントエンドでの実装例
const register = async (userData) => {
    const response = await axios.post('/api/register', userData);
    return response.data;
};
```

### 2. ログイン
```javascript
// フロントエンドでの実装例
const login = async (credentials) => {
    const response = await axios.post('/api/login', credentials);
    // トークンを保存
    localStorage.setItem('token', response.data.token);
    return response.data;
};
```

### 3. 認証済みリクエスト
```javascript
// フロントエンドでの実装例
const getAuthenticatedUser = async () => {
    const token = localStorage.getItem('token');
    const response = await axios.get('/api/user', {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    return response.data;
};
```

## 設定

### 1. Sanctum設定
```php
// config/sanctum.php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'expiration' => null,

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

### 2. 環境変数
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000
SANCTUM_TOKEN_EXPIRATION=null
```

## ベストプラクティス

### 1. トークン管理
- トークンは安全な方法で保存
- 定期的なトークンの更新
- 不要なトークンの無効化

### 2. セキュリティ
- HTTPSの使用
- パスワードのハッシュ化
- レート制限の実装
- セッションタイムアウトの設定

### 3. エラーハンドリング
- 適切なエラーメッセージ
- ログの記録
- セキュリティインシデントの監視

## トラブルシューティング

### 1. 認証エラー
- トークンの有効期限切れ
- 不正なトークン
- CSRFトークンの不一致

### 2. セッションエラー
- セッションタイムアウト
- セッションの破損
- クッキーの問題

### 3. パフォーマンス
- トークンの検証時間
- セッションの負荷
- データベースの負荷

## 実装例

### 1. 認証コントローラー
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ユーザー登録
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * ログイン
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['認証情報が正しくありません。'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }

    /**
     * ログインユーザー情報の取得
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
```

### 2. ルート設定
```php
// routes/api.php
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
```

### 3. ユーザーモデル
```php
// app/Models/User.php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

## Laravel Sanctumの詳細解説

### 1. Sanctumとは
Laravel Sanctumは、Laravelフレームワークの公式パッケージで、API認証を簡単に実装するためのツールです。以下の特徴があります：

- 軽量でシンプルな実装
- SPAとモバイルアプリケーションの両方に対応
- トークンベースの認証とセッションベースの認証の両方をサポート
- スコープベースの権限管理
- 複数のトークン管理

### 2. Sanctumの主要機能

#### 2.1 APIトークン認証
- ユーザーごとに複数のAPIトークンを発行可能
- トークンごとにスコープ（権限）を設定可能
- トークンの有効期限管理
- トークンの無効化機能

```php
// トークンの作成例
$token = $user->createToken('token-name', ['server:update']);

// スコープ付きトークンの検証
$user->tokenCan('server:update');
```

#### 2.2 SPA認証
- セッションベースの認証
- CSRF保護
- クロスオリジンリソース共有（CORS）のサポート
- セキュアなクッキーベースの認証

```php
// Sanctumの設定
'sanctum' => [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
    'expiration' => null,
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
],
```

### 3. Sanctumの認証フロー

#### 3.1 APIトークン認証のフロー
1. ユーザーがログイン
2. Sanctumがトークンを生成
3. クライアントがトークンを保存
4. 以降のリクエストでトークンをヘッダーに含めて送信
5. Sanctumがトークンを検証し、ユーザーを認証

#### 3.2 SPA認証のフロー
1. ユーザーがログイン
2. Sanctumがセッションクッキーを生成
3. ブラウザがクッキーを保存
4. 以降のリクエストでクッキーを自動送信
5. Sanctumがセッションを検証し、ユーザーを認証

### 4. Sanctumのセキュリティ機能

#### 4.1 トークン管理
- トークンの暗号化
- トークンの有効期限設定
- トークンの無効化機能
- トークンのスコープ管理

#### 4.2 CSRF保護
- セッションベースのCSRFトークン
- クロスオリジンリクエストの検証
- セキュアなクッキー設定

#### 4.3 セッション管理
- セッションの暗号化
- セッションタイムアウト
- セッションの無効化

### 5. Sanctumの設定

#### 5.1 基本設定
```php
// config/sanctum.php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
    'expiration' => null,
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

#### 5.2 環境変数
```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SANCTUM_TOKEN_EXPIRATION=null
```

### 6. Sanctumのベストプラクティス

#### 6.1 トークン管理
- トークンは安全な方法で保存（localStorageではなく、HttpOnlyクッキー）
- 定期的なトークンの更新
- 不要なトークンの無効化
- スコープの適切な設定

#### 6.2 セキュリティ
- HTTPSの使用
- 適切なCORS設定
- レート制限の実装
- セッションタイムアウトの設定

#### 6.3 パフォーマンス
- トークンの検証時間の最適化
- セッションの適切な管理
- データベースの負荷分散

### 7. Sanctumの利点

1. **シンプルな実装**
   - 軽量で理解しやすい
   - 最小限の設定で使用可能
   - 柔軟なカスタマイズが可能

2. **セキュリティ**
   - 堅牢な認証システム
   - セキュアなトークン管理
   - CSRF保護

3. **柔軟性**
   - SPAとモバイルアプリの両方に対応
   - カスタムスコープの設定
   - 拡張性が高い

4. **統合性**
   - Laravelフレームワークとの完全な統合
   - 他のLaravel機能との連携
   - 公式サポート 
