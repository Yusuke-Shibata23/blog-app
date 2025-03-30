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
            \Log::info('投稿一覧の取得を開始します', [
                'user' => Auth::user() ? Auth::user()->id : 'guest',
                'params' => $request->all()
            ]);

            $query = Post::with('user');

            // ログインしているユーザーの場合、自分の投稿のみを表示
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }

            // タグでフィルタリング
            if ($request->has('tags') && !empty($request->tags)) {
                $query->withTags($request->tags);
            }

            // キーワード検索
            if ($request->has('keyword') && !empty($request->keyword)) {
                $query->search($request->keyword);
            }

            $posts = $query->latest()->paginate(10);

            \Log::info('投稿一覧の取得が完了しました', [
                'count' => $posts->count(),
                'total' => $posts->total(),
                'user_id' => Auth::id()
            ]);

            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('投稿一覧の取得に失敗しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '投稿一覧の取得に失敗しました。',
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
            'images.*' => 'nullable|image|max:2048'
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

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('post-images', 'public');
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
        $this->authorize('view-post', $post);
        return response()->json($post->load('images'));
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
            $this->authorize('update-post', $post);

            \Log::info('投稿の更新を開始します', [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'status' => 'required|in:draft,published',
                'published_at' => 'nullable|date',
                'scheduled_at' => 'nullable|date|after:now',
                'images.*' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::warning('バリデーションエラー', [
                    'errors' => $validator->errors()
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post->update([
                'title' => $request->title,
                'content' => $request->content,
                'status' => $request->status,
                'published_at' => $request->published_at,
                'scheduled_at' => $request->scheduled_at
            ]);

            if ($request->hasFile('images')) {
                // 既存の画像を削除
                foreach ($post->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }

                // 新しい画像を保存
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('post-images', 'public');
                    $post->images()->create([
                        'image_path' => $path,
                        'order' => $index
                    ]);
                }
            }

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
     * Get draft posts.
     */
    public function drafts()
    {
        $posts = Post::with(['user', 'images'])
            ->where('user_id', auth()->id())
            ->draft()
            ->latest()
            ->paginate(10);

        return response()->json($posts);
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
} 