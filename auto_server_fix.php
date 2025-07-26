<?php
// 自動サーバー診断・修正スクリプト
// 404エラーの根本原因を特定して自動修正

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>自動サーバー修正</title>";
echo "<style>body{font-family:Arial;margin:20px;background:#f5f5f5;} .container{max-width:800px;margin:0 auto;background:white;padding:20px;border-radius:8px;} .success{color:#28a745;} .error{color:#dc3545;} .warning{color:#ffc107;} pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow-x:auto;}</style>";
echo "</head><body><div class='container'>";
echo "<h1>🔧 自動サーバー診断・修正スクリプト</h1>";
echo "<p><strong>実行時刻:</strong> " . date('Y-m-d H:i:s') . "</p>";

if (isset($_POST['auto_fix'])) {
    echo "<h2>🚀 自動修正実行中...</h2><pre>";
    
    try {
        echo "=== 自動サーバー修正開始 ===\n\n";
        
        // 1. 基本環境確認
        echo "1. 基本環境確認...\n";
        echo "PHP バージョン: " . phpversion() . "\n";
        echo "現在のディレクトリ: " . __DIR__ . "\n";
        echo "ドキュメントルート: " . ($_SERVER['DOCUMENT_ROOT'] ?? '不明') . "\n";
        echo "サーバー: " . ($_SERVER['HTTP_HOST'] ?? '不明') . "\n";
        echo "リクエストURI: " . ($_SERVER['REQUEST_URI'] ?? '不明') . "\n\n";
        
        // 2. .htaccess の確認と作成
        echo "2. .htaccess の確認と作成...\n";
        $htaccess_content = "RewriteEngine On\n";
        $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $htaccess_content .= "RewriteRule ^(.*)$ index.php [QSA,L]\n";
        $htaccess_content .= "DirectoryIndex index.php\n";
        $htaccess_content .= "Options -Indexes\n";
        
        if (file_put_contents('.htaccess', $htaccess_content)) {
            echo "✅ .htaccess ファイルを作成しました\n";
        } else {
            echo "⚠️ .htaccess ファイルの作成に失敗しました\n";
        }
        
        // 3. 簡単なindex.phpの作成
        echo "\n3. 基本index.phpの作成...\n";
        $index_content = "<?php\n";
        $index_content .= "// 基本的なLaravel bootstrap\n";
        $index_content .= "if (file_exists(__DIR__.'/vendor/autoload.php')) {\n";
        $index_content .= "    require __DIR__.'/vendor/autoload.php';\n";
        $index_content .= "    if (file_exists(__DIR__.'/bootstrap/app.php')) {\n";
        $index_content .= "        \$app = require_once __DIR__.'/bootstrap/app.php';\n";
        $index_content .= "        \$kernel = \$app->make(Illuminate\\Contracts\\Http\\Kernel::class);\n";
        $index_content .= "        \$response = \$kernel->handle(\n";
        $index_content .= "            \$request = Illuminate\\Http\\Request::capture()\n";
        $index_content .= "        );\n";
        $index_content .= "        \$response->send();\n";
        $index_content .= "        \$kernel->terminate(\$request, \$response);\n";
        $index_content .= "    } else {\n";
        $index_content .= "        echo '⚠️ Laravel bootstrap not found';\n";
        $index_content .= "    }\n";
        $index_content .= "} else {\n";
        $index_content .= "    echo '🔧 Server Test: PHP is working! Laravel vendor not found.';\n";
        $index_content .= "    echo '<br>Current directory: ' . __DIR__;\n";
        $index_content .= "    echo '<br>Document root: ' . \$_SERVER['DOCUMENT_ROOT'];\n";
        $index_content .= "    echo '<br>Server: ' . \$_SERVER['HTTP_HOST'];\n";
        $index_content .= "}\n";
        
        if (file_put_contents('index.php', $index_content)) {
            echo "✅ 基本index.phpを作成しました\n";
        } else {
            echo "⚠️ index.php の作成に失敗しました\n";
        }
        
        // 4. 権限設定
        echo "\n4. ディレクトリ権限の設定...\n";
        $dirs_to_fix = ['.', 'storage', 'bootstrap/cache'];
        foreach ($dirs_to_fix as $dir) {
            if (is_dir($dir)) {
                if (chmod($dir, 0755)) {
                    echo "✅ {$dir} の権限を755に設定\n";
                } else {
                    echo "⚠️ {$dir} の権限設定に失敗\n";
                }
            }
        }
        
        // 5. .env ファイルの作成
        echo "\n5. .env ファイルの確認と作成...\n";
        if (!file_exists('.env')) {
            $env_content = "APP_NAME=\"工程管理システム\"\n";
            $env_content .= "APP_ENV=production\n";
            $env_content .= "APP_KEY=base64:KOUTEI_SYSTEM_PRODUCTION_KEY_2025\n";
            $env_content .= "APP_DEBUG=false\n";
            $env_content .= "APP_URL=https://koutei.kiryu-factory.com\n\n";
            $env_content .= "DB_CONNECTION=mysql\n";
            $env_content .= "DB_HOST=localhost\n";
            $env_content .= "DB_PORT=3306\n";
            $env_content .= "DB_DATABASE=factory0328_wp2\n";
            $env_content .= "DB_USERNAME=factory0328_wp2\n";
            $env_content .= "DB_PASSWORD=ctwjr3mmf5\n";
            
            if (file_put_contents('.env', $env_content)) {
                echo "✅ .env ファイルを作成しました\n";
            } else {
                echo "⚠️ .env ファイルの作成に失敗しました\n";
            }
        } else {
            echo "✅ .env ファイルは既に存在します\n";
        }
        
        // 6. PHP情報の確認
        echo "\n6. PHP設定の確認...\n";
        echo "メモリ制限: " . ini_get('memory_limit') . "\n";
        echo "実行時間制限: " . ini_get('max_execution_time') . "\n";
        echo "ファイルアップロード: " . (ini_get('file_uploads') ? '有効' : '無効') . "\n";
        
        // 7. データベース接続テスト
        echo "\n7. データベース接続テスト...\n";
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=factory0328_wp2', 'factory0328_wp2', 'ctwjr3mmf5');
            echo "✅ データベース接続成功\n";
            
            // 必要なテーブルの確認
            $tables = ['users', 'wp_wqorders_editable'];
            foreach ($tables as $table) {
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                if ($stmt->fetch()) {
                    echo "✅ テーブル '{$table}' 存在確認\n";
                } else {
                    echo "⚠️ テーブル '{$table}' が見つかりません\n";
                }
            }
        } catch (Exception $e) {
            echo "❌ データベース接続失敗: " . $e->getMessage() . "\n";
        }
        
        // 8. 最終テスト用ファイル作成
        echo "\n8. 最終テスト用ファイル作成...\n";
        $test_content = "<?php echo '✅ PHP動作確認成功！<br>時刻: ' . date('Y-m-d H:i:s') . '<br>場所: ' . __DIR__; ?>";
        if (file_put_contents('final_test.php', $test_content)) {
            echo "✅ final_test.php を作成しました\n";
        }
        
        echo "\n🎉 自動修正完了！\n\n";
        echo "=== 次のテスト手順 ===\n";
        echo "1. https://koutei.kiryu-factory.com/ にアクセス\n";
        echo "2. https://koutei.kiryu-factory.com/final_test.php にアクセス\n";
        echo "3. どちらかが動作すれば修正成功\n";
        echo "4. 両方とも404の場合は、サーバー設定の根本的な問題\n";
        
    } catch (Exception $e) {
        echo "❌ エラー発生: " . $e->getMessage() . "\n";
    }
    
    echo "</pre>";
    
} else {
    echo "<h2>🚨 現在の問題</h2>";
    echo "<p>404エラーが継続しています。以下の自動修正を実行します：</p>";
    echo "<ul>";
    echo "<li>✅ .htaccess ファイルの作成・修正</li>";
    echo "<li>✅ 基本的なindex.phpの作成</li>";
    echo "<li>✅ ディレクトリ権限の設定</li>";
    echo "<li>✅ .env ファイルの作成</li>";
    echo "<li>✅ データベース接続テスト</li>";
    echo "<li>✅ PHP設定の確認</li>";
    echo "</ul>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='auto_fix' style='background:#007bff;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🚀 自動修正を実行</button>";
    echo "</form>";
}

echo "</div></body></html>";
?> 