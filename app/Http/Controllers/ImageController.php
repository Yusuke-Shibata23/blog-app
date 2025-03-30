<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * 画像をアップロードする
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            \Log::info('画像アップロードリクエスト開始', [
                'request' => $request->all(),
                'files' => $request->allFiles(),
                'headers' => $request->headers->all()
            ]);

            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,gif|max:10240'
            ]);

            $file = $request->file('image');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            \Log::info('画像ファイル情報', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'new_filename' => $fileName,
                'temp_path' => $file->getPathname(),
                'error' => $file->getError()
            ]);
            
            // 保存先ディレクトリの作成
            $storagePath = storage_path('app/public/images');
            $publicPath = public_path('storage/images');
            
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
                \Log::info('画像保存ディレクトリを作成しました', [
                    'storage_path' => $storagePath
                ]);
            }
            
            \Log::info('画像の保存を開始します', [
                'storage_path' => $storagePath,
                'public_path' => $publicPath,
                'storage_exists' => file_exists($storagePath),
                'public_exists' => file_exists($publicPath),
                'storage_writable' => is_writable($storagePath),
                'public_writable' => is_writable($publicPath)
            ]);
            
            // 画像を保存（直接ファイルシステムに保存）
            $targetPath = $storagePath . '/' . $fileName;
            if (!move_uploaded_file($file->getPathname(), $targetPath)) {
                throw new \Exception('画像の保存に失敗しました。');
            }
            
            \Log::info('画像の保存が完了しました', [
                'target_path' => $targetPath,
                'exists' => file_exists($targetPath),
                'size' => file_exists($targetPath) ? filesize($targetPath) : 0,
                'url' => '/storage/images/' . $fileName
            ]);
            
            // 保存した画像のURLを返す
            return response()->json([
                'url' => '/storage/images/' . $fileName,
                'path' => 'public/images/' . $fileName
            ]);
        } catch (\Exception $e) {
            \Log::error('画像アップロードエラー: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => '画像のアップロードに失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 