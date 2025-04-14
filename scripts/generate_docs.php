<?php

require __DIR__ . '/../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;

// 環境設定
$environment = new Environment([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new TableExtension());
$environment->addExtension(new HeadingPermalinkExtension());

$converter = new CommonMarkConverter([], $environment);

// テンプレート
$template = <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>%s</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .markdown-body {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .markdown-body pre {
            background-color: #f6f8fa;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }
        .markdown-body code {
            background-color: #f6f8fa;
            padding: 0.2em 0.4em;
            border-radius: 0.2em;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="markdown-body bg-white shadow-lg rounded-lg">
        %s
    </div>
</body>
</html>
HTML;

// docs/designディレクトリ内のMarkdownファイルを処理
$docsDir = __DIR__ . '/../docs/design';
$outputDir = __DIR__ . '/../public/docs';

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

function processDirectory($dir, $outputDir, $converter, $template) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            $newOutputDir = $outputDir . '/' . $file;
            if (!file_exists($newOutputDir)) {
                mkdir($newOutputDir, 0755, true);
            }
            processDirectory($path, $newOutputDir, $converter, $template);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $content = file_get_contents($path);
            $html = $converter->convert($content);
            $title = pathinfo($file, PATHINFO_FILENAME);

            $outputPath = $outputDir . '/' . $title . '.html';
            file_put_contents($outputPath, sprintf($template, $title, $html));
        }
    }
}

processDirectory($docsDir, $outputDir, $converter, $template);

echo "ドキュメントの生成が完了しました。\n";
