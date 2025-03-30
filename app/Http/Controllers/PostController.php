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
        $query = Post::with('user');

        // タグでフィルタリング
        if ($request->has('tags')) {
            $query->withTags($request->tags);
        }

        // キーワード検索
        if ($request->has('keyword')) {
            $query->search($request->keyword);
        }

        $posts = $query->latest()->paginate(10);

        return response()->json($posts);
    }

    /**
     * 投稿を作成
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
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