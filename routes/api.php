<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 認証関連のルート
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 認証が必要なルート
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 下書き・予約投稿の取得（先に定義）
    Route::get('/posts/drafts', [PostController::class, 'drafts']);
    Route::get('/posts/scheduled', [PostController::class, 'scheduled']);
    Route::get('/posts/published', [PostController::class, 'published']);
    Route::get('/posts/my', [PostController::class, 'myPosts']);

    // 投稿の作成・更新・削除・詳細表示
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // 画像アップロード
    Route::post('/upload-image', [ImageController::class, 'upload']);

    // いいね機能のルート
    Route::post('/posts/{post}/toggle-like', [PostController::class, 'toggleLike']);
    Route::get('/posts/{post}/like-status', [PostController::class, 'getLikeStatus']);
    Route::get('/posts/liked', [PostController::class, 'likedPosts']);
});

// 公開投稿一覧と詳細（認証不要）
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/public/{post}', [PostController::class, 'showPublic']); 