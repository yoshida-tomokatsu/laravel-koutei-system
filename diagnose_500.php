<?php
// 500エラー診断スクリプト
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Laravel 500エラー診断</h1>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>実行場所: " . __DIR__ . "</p>";

echo "<h2>📋 基本環境確認</h2>";
echo "<ul>";
echo "<li>PHPバージョン: " . phpversion() . "</li>";
echo "<li>現在のディレクトリ: " . __DIR__ . "</li>";
echo "<li>ドキュメントルート: " . ($_SERVER['DOCUMENT_ROOT'] ?? '不明') . "</li>";
echo "</ul>";

echo "<h2>🔧 Laravel必須ファイル確認</h2>";
$files = [
    'vendor/autoload.php' => 'Composer オートローダー',
    'bootstrap/app.php' => 'Laravel Bootstrap',
    '.env' => '環境設定ファイル',
    'public/index.php' => 'Public Index',
    'app/Http/Kernel.php' => 'HTTP Kernel'
];

foreach ($files as $file => $description) {
    $exists = file_exists($file);
    $status = $exists ? "✅ 存在" : "❌ 不在";
    echo "<p>{$status} {$description} ({$file})</p>";
    
    if ($exists && $file === '.env') {
        echo "<details><summary>📄 .env ファイル内容</summary><pre>";
        $env_content = file_get_contents($file);
        $lines = explode("\n", $env_content);
        foreach ($lines as $line) {
            if (strpos($line, 'DB_PASSWORD') !== false) {
                echo "DB_PASSWORD=***隠***\n";
            } else {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo "</pre></details>";
    }
}

echo "<h2>🗂 ディレクトリ構造確認</h2>";
$dirs = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'vendor'];
echo "<ul>";
foreach ($dirs as $dir) {
    $exists = is_dir($dir);
    $status = $exists ? "✅" : "❌";
    echo "<li>{$status} {$dir}/</li>";
}
echo "</ul>";

echo "<h2>🚀 Laravel起動テスト</h2>";

try {
    echo "<p>🔄 vendor/autoload.php の読み込み中...</p>";
    
    if (file_exists('vendor/autoload.php')) {
        require 'vendor/autoload.php';
        echo "<p>✅ vendor/autoload.php 読み込み成功</p>";
        
        if (file_exists('bootstrap/app.php')) {
            echo "<p>🔄 bootstrap/app.php の読み込み中...</p>";
            $app = require_once 'bootstrap/app.php';
            echo "<p>✅ bootstrap/app.php 読み込み成功</p>";
            
            echo "<p>🔄 HTTP Kernel の作成中...</p>";
            $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
            echo "<p>✅ HTTP Kernel 作成成功</p>";
            
            echo "<p>🔄 リクエストの処理中...</p>";
            $request = Illuminate\Http\Request::capture();
            echo "<p>✅ リクエスト作成成功</p>";
            
            echo "<p>🔄 レスポンスの生成中...</p>";
            $response = $kernel->handle($request);
            echo "<p>✅ レスポンス生成成功</p>";
            
            echo "<h3>🎉 Laravel正常起動！</h3>";
            echo "<p>500エラーは一時的な問題の可能性があります。</p>";
            
        } else {
            echo "<p>❌ bootstrap/app.php が見つかりません</p>";
        }
    } else {
        echo "<p>❌ vendor/autoload.php が見つかりません</p>";
        echo "<p>🔧 解決方法: composer install を実行してください</p>";
    }
    
} catch (Exception $e) {
    echo "<h3>❌ Laravel起動エラー</h3>";
    echo "<p><strong>エラーメッセージ:</strong></p>";
    echo "<pre style='background:#ffebee;padding:10px;border-radius:4px;'>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
    
    echo "<p><strong>ファイル:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>行:</strong> " . $e->getLine() . "</p>";
    
    echo "<details><summary>📋 詳細スタックトレース</summary><pre>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre></details>";
}

echo "<h2>🔧 データベース接続テスト</h2>";
try {
    if (file_exists('.env')) {
        $env_content = file_get_contents('.env');
        preg_match('/DB_HOST=(.*)/', $env_content, $host_match);
        preg_match('/DB_DATABASE=(.*)/', $env_content, $db_match);
        preg_match('/DB_USERNAME=(.*)/', $env_content, $user_match);
        preg_match('/DB_PASSWORD=(.*)/', $env_content, $pass_match);
        
        $host = trim($host_match[1] ?? 'localhost');
        $database = trim($db_match[1] ?? '');
        $username = trim($user_match[1] ?? '');
        $password = trim($pass_match[1] ?? '');
        
        if ($database && $username) {
            $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
            echo "<p>✅ データベース接続成功</p>";
            echo "<p>データベース: {$database}</p>";
        } else {
            echo "<p>⚠️ データベース設定が不完全です</p>";
        }
    } else {
        echo "<p>❌ .env ファイルが見つかりません</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ データベース接続失敗: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>💡 推奨対応</h2>";
echo "<ol>";
echo "<li>上記のエラーメッセージを確認</li>";
echo "<li>不足しているファイル・ディレクトリを補完</li>";
echo "<li>データベース設定を確認</li>";
echo "<li>必要に応じて composer install を実行</li>";
echo "</ol>";
?> 