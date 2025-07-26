<?php
// サブドメイン用Laravel起動スクリプト
// /kiryu-factory.com/public_html/koutei/index.php

echo "<h1>🔧 Laravel起動テスト</h1>";
echo "<p>現在時刻: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>現在のディレクトリ: " . __DIR__ . "</p>";

// Laravel環境の確認と起動
if (file_exists(__DIR__.'/vendor/autoload.php')) {
    echo "<p style='color:green;'>✅ vendor/autoload.php 検出</p>";
    
    try {
        require __DIR__.'/vendor/autoload.php';
        
        if (file_exists(__DIR__.'/bootstrap/app.php')) {
            echo "<p style='color:green;'>✅ bootstrap/app.php 検出</p>";
            
            $app = require_once __DIR__.'/bootstrap/app.php';
            
            echo "<p style='color:blue;'>🚀 Laravel起動中...</p>";
            
            $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
            
            $response = $kernel->handle(
                $request = Illuminate\Http\Request::capture()
            );
            
            echo "<p style='color:green;'>✅ Laravel正常起動！</p>";
            echo "<hr>";
            
            $response->send();
            
            $kernel->terminate($request, $response);
            
        } else {
            echo "<p style='color:red;'>❌ bootstrap/app.php が見つかりません</p>";
            echo "<p>Laravelプロジェクトの構造を確認してください。</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Laravel起動エラー:</p>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<p>基本PHPとして動作します。</p>";
    }
    
} else {
    echo "<p style='color:orange;'>⚠️ vendor/autoload.php が見つかりません</p>";
    echo "<p>composer install が必要です。</p>";
    
    // ファイル一覧表示
    echo "<h3>📁 現在のファイル一覧:</h3>";
    echo "<ul>";
    $files = glob('*');
    foreach ($files as $file) {
        $type = is_file($file) ? 'ファイル' : 'ディレクトリ';
        echo "<li>{$file} ({$type})</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><strong>🔧 デバッグ情報:</strong></p>";
echo "<ul>";
echo "<li>サーバー: " . ($_SERVER['HTTP_HOST'] ?? '不明') . "</li>";
echo "<li>リクエストURI: " . ($_SERVER['REQUEST_URI'] ?? '不明') . "</li>";
echo "<li>PHPバージョン: " . phpversion() . "</li>";
echo "</ul>";
?> 