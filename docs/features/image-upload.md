# 画像アップロード機能の実装メモ

## 概要
Markdownエディターで画像をアップロードし、プレビュー表示する機能の実装に関する記録です。

## 技術スタック
- フロントエンド: Vue.js + Markdown-it
- バックエンド: Laravel
- ストレージ: ローカルファイルシステム

## 実装のポイント

### 1. ストレージ構造
```
storage/
  └── app/
      └── public/
          └── images/  # 実際のファイル保存先
public/
  └── storage/        # シンボリックリンク
      └── images/     # 公開アクセス用
```

### 2. シンボリックリンクの設定
```bash
php artisan storage:link --force
```
- `storage/app/public` → `public/storage`へのシンボリックリンクを作成
- セキュリティのため、`storage`ディレクトリは直接Webからアクセス不可

### 3. 画像保存処理
```php
// 保存先ディレクトリの作成
$storagePath = storage_path('app/public/images');
if (!file_exists($storagePath)) {
    mkdir($storagePath, 0755, true);
}

// 画像の保存
$targetPath = $storagePath . '/' . $fileName;
move_uploaded_file($file->getPathname(), $targetPath);
```

### 4. アクセスURL
- 保存された画像へのアクセスURL: `/storage/images/ファイル名`
- 例: `/storage/images/e02f9c0f-a237-4bcd-80dc-2d650f4908d7.png`

## 注意点

### 1. パーミッション
- `storage/app/public/images`ディレクトリに書き込み権限が必要
- Windows環境では`move_uploaded_file`を使用して直接ファイルシステムに保存

### 2. バリデーション
```php
$request->validate([
    'image' => 'required|image|mimes:jpeg,png,gif|max:10240'
]);
```
- 対応フォーマット: JPEG, PNG, GIF
- 最大サイズ: 10MB

### 3. ファイル名
- UUIDを使用してユニークなファイル名を生成
- 拡張子は元のファイルから取得

## トラブルシューティング

### 1. 画像が表示されない場合
1. シンボリックリンクが正しく作成されているか確認
2. `storage/app/public/images`にファイルが保存されているか確認
3. `public/storage/images`にファイルが表示されているか確認

### 2. アップロードに失敗する場合
1. ディレクトリのパーミッションを確認
2. ファイルサイズが制限を超えていないか確認
3. 対応フォーマットの画像か確認

## 今後の改善点
1. 画像の最適化処理の追加
2. 不要な画像の自動削除機能
3. 画像のプレビュー機能の強化
4. ドラッグ&ドロップでのアップロード対応 