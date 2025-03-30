<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
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
        try {
            \Log::info('Post creation request:', $request->all());

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string',
            ]);

            \Log::info('Validated data:', $validated);

            // タグが空の場合は空配列を設定
            if (empty($validated['tags'])) {
                $validated['tags'] = [];
            }

            // タグの空文字を除去
            $validated['tags'] = array_filter($validated['tags'], function($tag) {
                return !empty(trim($tag));
            });

            \Log::info('Processed tags:', ['tags' => $validated['tags']]);

            $post = Auth::user()->posts()->create($validated);

            \Log::info('Post created successfully:', ['post_id' => $post->id]);

            return response()->json($post->load('user'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'バリデーションエラー',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Post creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '投稿の作成に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 投稿を取得
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        return response()->json($post->load('user'));
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
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    /**
     * 投稿を削除
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
} 