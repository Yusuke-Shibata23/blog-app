<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JSONファイルから投稿データを読み込む
        $jsonPath = database_path('seeds/data/posts.json');
        $postsData = json_decode(file_get_contents($jsonPath), true)['posts'];

        foreach ($postsData as $postData) {
            // Markdownファイルから本文を読み込む
            $contentPath = database_path("seeds/data/content/{$postData['content_file']}");
            $content = file_get_contents($contentPath);

            // ユーザーをメールアドレスで検索
            $user = User::where('email', $postData['author_email'])->first();
            if (!$user) {
                continue;
            }

            // 投稿データを作成
            $post = new Post();
            $post->title = $postData['title'];
            $post->content = $content;
            $post->status = $postData['status'] ?? 'published';
            $post->tags = json_encode($postData['tags'], JSON_UNESCAPED_UNICODE);
            $post->user_id = $user->id;

            // 公開日時の設定
            if (isset($postData['published_at'])) {
                $post->published_at = Carbon::parse($postData['published_at']);
            }

            $post->save();

            // 画像の処理
            if (isset($postData['images'])) {
                foreach ($postData['images'] as $imageData) {
                    $this->processImage($post, $imageData);
                }
            }
        }
    }

    /**
     * 画像を処理して保存する
     */
    private function processImage(Post $post, array $imageData): void
    {
        // シードデータの画像ファイルパス
        $sourcePath = database_path("seeds/data/images/posts/{$post->id}/{$imageData['filename']}");
        
        if (!file_exists($sourcePath)) {
            return;
        }

        // ストレージに画像をコピー
        $destinationPath = "posts/{$post->id}/{$imageData['filename']}";
        Storage::putFileAs(
            'public',
            $sourcePath,
            $destinationPath
        );

        // データベースに画像情報を保存
        PostImage::create([
            'post_id' => $post->id,
            'filename' => $imageData['filename'],
            'path' => $destinationPath,
            'alt' => $imageData['alt'] ?? '',
            'caption' => $imageData['caption'] ?? null,
        ]);
    }
} 