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
                'new_filename' => $fileName
            ]);
            
            // 画像を保存（Laravelのストレージシステムを使用）
            $path = $file->storeAs('images', $fileName, 'public');
            
            \Log::info('画像の保存が完了しました', [
                'path' => $path,
                'url' => Storage::url($path)
            ]);
            
            // 保存した画像のURLを返す
            return response()->json([
                'url' => Storage::url($path),
                'path' => $path
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