<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PostController;

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

    // 投稿の作成・更新・削除
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});

// 公開投稿一覧・詳細（認証不要）
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']); 