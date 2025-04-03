<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * 投稿一覧を取得
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            \Log::info('公開投稿一覧の取得を開始します', [
                'user' => Auth::user() ? Auth::user()->id : 'guest',
                'params' => $request->all()
            ]);

            $query = Post::query();

            // 検索クエリがある場合
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // 公開済みの記事のみを取得
            $query->where('status', 'published')
                  ->where(function($q) {
                      $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', now());
                  });

            // タグでフィルタリング
            if ($request->has('tags') && !empty($request->tags)) {
                $query->withTags($request->tags);
            }

            $posts = $query->latest()->paginate(10);

            \Log::info('公開投稿一覧の取得が完了しました', [
                'count' => $posts->count(),
                'total' => $posts->total()
            ]);

            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('公開投稿一覧の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '公開投稿一覧の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 投稿を作成
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'scheduled_at' => 'nullable|date|after:now',
            'images.*' => 'nullable|image|max:2048',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'published_at' => $request->published_at,
            'scheduled_at' => $request->scheduled_at,
            'user_id' => auth()->id()
        ]);

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->update(['thumbnail_path' => $thumbnailPath]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('images', 'public');
                $post->images()->create([
                    'image_path' => $path,
                    'order' => $index
                ]);
            }
        }

        return response()->json($post->load('images'), 201);
    }

    /**
     * 投稿を取得
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        try {
            \Log::info('投稿の取得を開始します', [
                'post_id' => $post->id,
                'user' => Auth::user() ? Auth::user()->id : 'guest',
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);

            // 公開記事の場合は認可チェックをスキップ
            if ($post->status !== 'published') {
                $this->authorize('viewPost', $post);
            }

            $post->load(['user', 'images']);

            \Log::info('投稿の取得が完了しました', [
                'post_id' => $post->id,
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);

            return response()->json($post);
        } catch (\Illuminate\Auth\AuthorizationException $e) {
            \Log::error('認可エラー', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);
            return response()->json([
                'message' => 'この投稿を閲覧する権限がありません。',
                'error' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            \Log::error('投稿の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);
            return response()->json([
                'message' => '投稿の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 投稿を更新
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        try {
            $this->authorize('update', $post);

            \Log::info('投稿の更新を開始します', [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'request_files' => $request->allFiles(),
                'request_headers' => $request->headers->all()
            ]);

            // デバッグ用：リクエストの詳細をログに記録
            \Log::debug('リクエストの詳細', [
                'hasFile_thumbnail' => $request->hasFile('thumbnail'),
                'thumbnail' => $request->file('thumbnail'),
                'allFiles' => $request->allFiles(),
                'all' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'status' => 'required|in:draft,published',
                'images.*' => 'nullable|file|mimes:jpeg,png,gif|max:10240',
                'thumbnail' => 'nullable|file|mimes:jpeg,png,gif|max:2048',
                'thumbnail_path' => 'nullable|string',
                'delete_thumbnail' => 'nullable|boolean'
            ], [
                'title.required' => 'タイトルは必須です。',
                'title.max' => 'タイトルは255文字以内で入力してください。',
                'content.required' => '内容は必須です。',
                'status.required' => 'ステータスは必須です。',
                'status.in' => '無効なステータスです。',
                'images.*.file' => '画像ファイルをアップロードしてください。',
                'images.*.mimes' => '画像はJPEG、PNG、GIF形式のみアップロード可能です。',
                'images.*.max' => '画像サイズは10MB以下にしてください。',
                'thumbnail.file' => 'サムネイル画像をアップロードしてください。',
                'thumbnail.mimes' => 'サムネイル画像はJPEG、PNG、GIF形式のみアップロード可能です。',
                'thumbnail.max' => 'サムネイル画像サイズは2MB以下にしてください。',
                'delete_thumbnail.boolean' => '無効な削除オプションです。'
            ]);

            if ($validator->fails()) {
                \Log::warning('バリデーションエラー', [
                    'errors' => $validator->errors(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'message' => '入力内容に問題があります。',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Markdownコンテンツの処理
            $content = $request->content;
            
            // 画像の処理
            if ($request->has('deleted_image_ids')) {
                $deletedImageIds = json_decode($request->deleted_image_ids, true);
                foreach ($deletedImageIds as $imageId) {
                    $image = $post->images()->find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // サムネイル画像の処理
            \Log::info('サムネイル画像の処理を開始します', [
                'hasFile' => $request->hasFile('thumbnail'),
                'files' => $request->allFiles(),
                'request_data' => $request->all()
            ]);

            if ($request->hasFile('thumbnail')) {
                // 古いサムネイルを削除
                if ($post->thumbnail_path) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }
                // 新しいサムネイルを保存
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                \Log::info('サムネイル画像を保存しました', [
                    'thumbnail_path' => $thumbnailPath
                ]);
                $post->update(['thumbnail_path' => $thumbnailPath]);
            } else if ($request->has('delete_thumbnail') && $request->delete_thumbnail === '1') {
                // サムネイル画像の削除
                if ($post->thumbnail_path) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                    $post->update(['thumbnail_path' => null]);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('images', 'public');
                    $post->images()->create([
                        'image_path' => $path,
                        'order' => $index
                    ]);
                }
            }

            // 投稿の更新
            $post->update([
                'title' => $request->title,
                'content' => $content,
                'status' => $request->status
            ]);

            \Log::info('投稿の更新が完了しました', [
                'post_id' => $post->id
            ]);

            return response()->json($post->load('images'));
        } catch (\Illuminate\Auth\AuthorizationException $e) {
            \Log::error('認可エラー', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'message' => 'この投稿を編集する権限がありません。',
                'error' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            \Log::error('投稿の更新に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'post_id' => $post->id,
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'message' => '投稿の更新に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 投稿を削除
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete-post', $post);

        // 画像ファイルを削除
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $post->delete();
        return response()->json(null, 204);
    }

    /**
     * 下書き一覧を取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function drafts()
    {
        try {
            \Log::info('下書き一覧の取得を開始します', [
                'user' => Auth::user() ? Auth::user()->id : 'guest'
            ]);

            $posts = Post::with('user')
                ->where('user_id', Auth::id())
                ->where('status', 'draft')
                ->latest()
                ->paginate(10);

            \Log::info('下書き一覧の取得が完了しました', [
                'count' => $posts->count(),
                'total' => $posts->total(),
                'user_id' => Auth::id()
            ]);

            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('下書き一覧の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '下書き一覧の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scheduled posts.
     */
    public function scheduled()
    {
        $posts = Post::with(['user', 'images'])
            ->where('user_id', auth()->id())
            ->scheduled()
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }

    /**
     * 公開中の投稿一覧を取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function published()
    {
        try {
            \Log::info('公開中の投稿一覧の取得を開始します', [
                'user' => Auth::user() ? Auth::user()->id : 'guest'
            ]);

            $posts = Post::with('user')
                ->where('user_id', Auth::id())
                ->where('status', 'published')
                ->latest()
                ->paginate(10);

            \Log::info('公開中の投稿一覧の取得が完了しました', [
                'count' => $posts->count(),
                'total' => $posts->total(),
                'user_id' => Auth::id()
            ]);

            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('公開中の投稿一覧の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '公開中の投稿一覧の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 自分の投稿一覧を取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myPosts()
    {
        try {
            \Log::info('自分の投稿一覧の取得を開始します', [
                'user' => Auth::user() ? Auth::user()->id : 'guest'
            ]);

            $posts = Post::with('user')
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(10);

            \Log::info('自分の投稿一覧の取得が完了しました', [
                'count' => $posts->count(),
                'total' => $posts->total(),
                'user_id' => Auth::id()
            ]);

            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('自分の投稿一覧の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '自分の投稿一覧の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 公開記事の詳細を取得
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPublic(Post $post)
    {
        try {
            \Log::info('公開記事の取得を開始します', [
                'post_id' => $post->id,
                'user' => Auth::user() ? Auth::user()->id : 'guest',
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);

            // 公開記事でない場合は404
            if ($post->status !== 'published' || 
                ($post->scheduled_at !== null && $post->scheduled_at > now())) {
                return response()->json([
                    'message' => '記事が見つかりません。'
                ], 404);
            }

            $post->load(['user', 'images']);

            \Log::info('公開記事の取得が完了しました', [
                'post_id' => $post->id,
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);

            return response()->json($post);
        } catch (\Exception $e) {
            \Log::error('公開記事の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'post_status' => $post->status,
                'post_user_id' => $post->user_id
            ]);
            return response()->json([
                'message' => '記事の取得に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 