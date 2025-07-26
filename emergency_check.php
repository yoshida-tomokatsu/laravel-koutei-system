<?php
// 緊急用Laravel環境チェックスクリプト
echo "<h1>🚨 緊急Laravel環境チェック</h1>";
echo "<pre>";

echo "=== 基本環境情報 ===\n";
echo "PHP バージョン: " . phpversion() . "\n";
echo "現在時刻: " . date('Y-m-d H:i:s') . "\n";
echo "サーバー: " . $_SERVER['HTTP_HOST'] . "\n";
echo "現在のディレクトリ: " . __DIR__ . "\n";
echo "ドキュメントルート: " . $_SERVER['DOCUMENT_ROOT'] . "\n\n";

echo "=== ファイル存在確認 ===\n";
$files = [
    'artisan' => 'Laravel Artisan コマンド',
    '.env' => '環境設定ファイル',
    'composer.json' => 'Composer設定',
    'vendor/autoload.php' => 'Composer オートローダー',
    'bootstrap/app.php' => 'Laravel Bootstrap',
    'public/index.php' => 'Laravel エントリーポイント',
    'app/Http/Kernel.php' => 'HTTP Kernel'
];

foreach ($files as $file => $description) {
    $exists = file_exists($file);
    echo ($exists ? "✅" : "❌") . " {$description}: " . ($exists ? "存在" : "不在") . "\n";
}

echo "\n=== ディレクトリ構造 ===\n";
$dirs = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'vendor'];
foreach ($dirs as $dir) {
    $exists = is_dir($dir);
    echo ($exists ? "✅" : "❌") . " {$dir}/: " . ($exists ? "存在" : "不在") . "\n";
}

echo "\n=== .env ファイル内容確認 ===\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    $lines = explode("\n", $env_content);
    foreach ($lines as $line) {
        if (strpos($line, 'APP_') === 0 || strpos($line, 'DB_') === 0) {
            echo $line . "\n";
        }
    }
} else {
    echo "❌ .env ファイルが存在しません\n";
}

echo "\n=== PHP拡張モジュール ===\n";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo ($loaded ? "✅" : "❌") . " {$ext}: " . ($loaded ? "有効" : "無効") . "\n";
}

echo "\n=== 権限確認 ===\n";
$writable_dirs = ['storage', 'bootstrap/cache'];
foreach ($writable_dirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir);
        echo ($writable ? "✅" : "❌") . " {$dir}: " . ($writable ? "書き込み可能" : "書き込み不可") . "\n";
    } else {
        echo "❌ {$dir}: ディレクトリが存在しません\n";
    }
}

echo "\n=== 推奨対応 ===\n";
if (!file_exists('vendor/autoload.php')) {
    echo "🔧 composer install を実行してください\n";
}
if (!file_exists('.env')) {
    echo "🔧 .env ファイルを作成してください\n";
}
if (!file_exists('public/index.php')) {
    echo "🔧 public/index.php が見つかりません - Laravel構造を確認\n";
}

echo "</pre>";
?> 