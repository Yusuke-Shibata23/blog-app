<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * 投稿の閲覧が許可されているかを判定
     *
     * @param User|null $user
     * @param Post $post
     * @return bool
     */
    public function viewPost(?User $user, Post $post)
    {
        // 公開済みの投稿は誰でも閲覧可能
        if ($post->status === 'published' && 
            ($post->scheduled_at === null || $post->scheduled_at <= now())) {
            return true;
        }

        // 未ログインユーザーは非公開投稿を閲覧できない
        if (!$user) {
            return false;
        }

        // 投稿者は自分の投稿を閲覧可能
        return $user->id === $post->user_id;
    }

    /**
     * 投稿の更新が許可されているかを判定
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }

    /**
     * 投稿の削除が許可されているかを判定
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function delete(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }
} 