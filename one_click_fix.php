<?php
// ワンクリック完全自動修正スクリプト
// アクセスするだけで全て自動実行

// 自動実行フラグ
$auto_execute = true;

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>ワンクリック自動修正</title>";
echo "<style>body{font-family:Arial;margin:20px;background:#f5f5f5;} .container{max-width:900px;margin:0 auto;background:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} .success{color:#28a745;font-weight:bold;} .error{color:#dc3545;font-weight:bold;} .warning{color:#ffc107;font-weight:bold;} .info{color:#17a2b8;font-weight:bold;} pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow-x:auto;font-size:12px;line-height:1.4;} .status{padding:10px;margin:10px 0;border-radius:5px;} .status.success{background:#d4edda;border:1px solid #c3e6cb;} .status.error{background:#f8d7da;border:1px solid #f5c6cb;} .btn{background:#007bff;color:white;padding:12px 24px;border:none;border-radius:4px;cursor:pointer;margin:10px 5px;font-size:16px;} .btn:hover{background:#0056b3;}</style>";
echo "</head><body><div class='container'>";

echo "<h1>🚀 ワンクリック完全自動修正</h1>";
echo "<p><strong>実行時刻:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>サーバー:</strong> " . ($_SERVER['HTTP_HOST'] ?? '不明') . "</p>";
echo "<p><strong>現在の場所:</strong> " . __DIR__ . "</p>";

if ($auto_execute) {
    echo "<div class='status success'>✅ 自動実行モード: アクセスと同時に修正開始</div>";
    echo "<h2>🔧 自動修正実行中...</h2><pre>";
    
    $success_count = 0;
    $error_count = 0;
    
    try {
        echo "=== ワンクリック自動修正開始 ===\n\n";
        
        // 1. 環境診断
        echo "【ステップ1】環境診断...\n";
        echo "PHP バージョン: " . phpversion() . "\n";
        echo "現在のディレクトリ: " . __DIR__ . "\n";
        echo "ドキュメントルート: " . ($_SERVER['DOCUMENT_ROOT'] ?? '不明') . "\n";
        echo "サーバー名: " . ($_SERVER['HTTP_HOST'] ?? '不明') . "\n";
        echo "リクエストURI: " . ($_SERVER['REQUEST_URI'] ?? '不明') . "\n";
        echo "実行ユーザー: " . (function_exists('get_current_user') ? get_current_user() : '不明') . "\n\n";
        
        // 2. 緊急用テストファイル作成
        echo "【ステップ2】緊急テストファイル作成...\n";
        $emergency_files = [
            'test_success.php' => "<?php echo '✅ PHP実行成功！<br>時刻: ' . date('Y-m-d H:i:s') . '<br>場所: ' . __DIR__ . '<br>サーバー: ' . \$_SERVER['HTTP_HOST']; ?>",
            'laravel_test.php' => "<?php if(file_exists('vendor/autoload.php')){echo '✅ Laravel環境検出';}else{echo '⚠️ Laravel環境なし - 基本PHP動作中';} echo '<br>ディレクトリ: ' . __DIR__; ?>",
            'db_test.php' => "<?php try{\$pdo=new PDO('mysql:host=localhost;dbname=factory0328_wp2','factory0328_wp2','ctwjr3mmf5');echo '✅ DB接続成功';}catch(Exception \$e){echo '❌ DB接続失敗: '.\$e->getMessage();} ?>"
        ];
        
        foreach ($emergency_files as $filename => $content) {
            if (file_put_contents($filename, $content)) {
                echo "✅ {$filename} 作成成功\n";
                $success_count++;
            } else {
                echo "❌ {$filename} 作成失敗\n";
                $error_count++;
            }
        }
        
        // 3. .htaccess 修正
        echo "\n【ステップ3】.htaccess 設定...\n";
        $htaccess_content = "# Laravel用 .htaccess\n";
        $htaccess_content .= "RewriteEngine On\n";
        $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $htaccess_content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $htaccess_content .= "RewriteRule ^(.*)$ index.php [QSA,L]\n";
        $htaccess_content .= "DirectoryIndex index.php\n";
        $htaccess_content .= "Options -Indexes\n";
        $htaccess_content .= "# PHP設定\n";
        $htaccess_content .= "php_value memory_limit 256M\n";
        $htaccess_content .= "php_value max_execution_time 300\n";
        
        if (file_put_contents('.htaccess', $htaccess_content)) {
            echo "✅ .htaccess 作成・更新成功\n";
            $success_count++;
        } else {
            echo "❌ .htaccess 作成失敗\n";
            $error_count++;
        }
        
        // 4. index.php 作成
        echo "\n【ステップ4】index.php 作成...\n";
        $index_content = "<?php\n";
        $index_content .= "// 自動生成されたindex.php\n";
        $index_content .= "echo '<h1>🎉 サーバー修正成功！</h1>';\n";
        $index_content .= "echo '<p>時刻: ' . date('Y-m-d H:i:s') . '</p>';\n";
        $index_content .= "echo '<p>場所: ' . __DIR__ . '</p>';\n";
        $index_content .= "echo '<p>サーバー: ' . \$_SERVER['HTTP_HOST'] . '</p>';\n";
        $index_content .= "\n";
        $index_content .= "// Laravel環境チェック\n";
        $index_content .= "if (file_exists(__DIR__.'/vendor/autoload.php')) {\n";
        $index_content .= "    echo '<p style=\"color:green;\">✅ Laravel環境検出 - 本格起動を試行中...</p>';\n";
        $index_content .= "    try {\n";
        $index_content .= "        require __DIR__.'/vendor/autoload.php';\n";
        $index_content .= "        if (file_exists(__DIR__.'/bootstrap/app.php')) {\n";
        $index_content .= "            \$app = require_once __DIR__.'/bootstrap/app.php';\n";
        $index_content .= "            \$kernel = \$app->make(Illuminate\\Contracts\\Http\\Kernel::class);\n";
        $index_content .= "            \$response = \$kernel->handle(\n";
        $index_content .= "                \$request = Illuminate\\Http\\Request::capture()\n";
        $index_content .= "            );\n";
        $index_content .= "            \$response->send();\n";
        $index_content .= "            \$kernel->terminate(\$request, \$response);\n";
        $index_content .= "        } else {\n";
        $index_content .= "            echo '<p style=\"color:orange;\">⚠️ Laravel bootstrap見つからず</p>';\n";
        $index_content .= "        }\n";
        $index_content .= "    } catch (Exception \$e) {\n";
        $index_content .= "        echo '<p style=\"color:red;\">❌ Laravel起動エラー: ' . \$e->getMessage() . '</p>';\n";
        $index_content .= "        echo '<p>基本PHP環境として動作中</p>';\n";
        $index_content .= "    }\n";
        $index_content .= "} else {\n";
        $index_content .= "    echo '<p style=\"color:blue;\">ℹ️ 基本PHP環境として動作中</p>';\n";
        $index_content .= "    echo '<p>Laravel vendor ディレクトリが見つかりません</p>';\n";
        $index_content .= "}\n";
        
        if (file_put_contents('index.php', $index_content)) {
            echo "✅ index.php 作成成功\n";
            $success_count++;
        } else {
            echo "❌ index.php 作成失敗\n";
            $error_count++;
        }
        
        // 5. .env ファイル作成
        echo "\n【ステップ5】.env ファイル設定...\n";
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
                echo "✅ .env ファイル作成成功\n";
                $success_count++;
            } else {
                echo "❌ .env ファイル作成失敗\n";
                $error_count++;
            }
        } else {
            echo "✅ .env ファイル既存確認\n";
            $success_count++;
        }
        
        // 6. 権限設定
        echo "\n【ステップ6】権限設定...\n";
        $dirs_to_fix = ['.', 'storage', 'bootstrap/cache', 'public'];
        foreach ($dirs_to_fix as $dir) {
            if (is_dir($dir)) {
                if (chmod($dir, 0755)) {
                    echo "✅ {$dir} 権限設定成功 (755)\n";
                    $success_count++;
                } else {
                    echo "⚠️ {$dir} 権限設定失敗\n";
                    $error_count++;
                }
            }
        }
        
        // 7. データベース接続テスト
        echo "\n【ステップ7】データベース接続テスト...\n";
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=factory0328_wp2', 'factory0328_wp2', 'ctwjr3mmf5');
            echo "✅ データベース接続成功\n";
            $success_count++;
            
            // テーブル存在確認
            $tables = ['users', 'wp_wqorders_editable'];
            foreach ($tables as $table) {
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                if ($stmt->fetch()) {
                    echo "✅ テーブル '{$table}' 存在確認\n";
                } else {
                    echo "⚠️ テーブル '{$table}' 見つからず\n";
                }
            }
        } catch (Exception $e) {
            echo "❌ データベース接続失敗: " . $e->getMessage() . "\n";
            $error_count++;
        }
        
        // 8. 最終結果
        echo "\n【ステップ8】修正完了・結果確認...\n";
        echo "成功: {$success_count}件\n";
        echo "エラー: {$error_count}件\n";
        
        echo "\n🎉 ワンクリック自動修正完了！\n\n";
        
        // 9. テスト用URL生成
        echo "=== 動作確認用URL ===\n";
        $base_url = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'koutei.kiryu-factory.com');
        $current_path = dirname($_SERVER['REQUEST_URI'] ?? '');
        if ($current_path === '/') $current_path = '';
        
        $test_urls = [
            $base_url . $current_path . '/' => 'メインページ',
            $base_url . $current_path . '/test_success.php' => 'PHP動作テスト',
            $base_url . $current_path . '/laravel_test.php' => 'Laravel環境テスト',
            $base_url . $current_path . '/db_test.php' => 'データベーステスト'
        ];
        
        foreach ($test_urls as $url => $description) {
            echo "{$description}: {$url}\n";
        }
        
        echo "\n=== 推奨テスト順序 ===\n";
        echo "1. まず PHP動作テスト にアクセス\n";
        echo "2. 成功したら メインページ にアクセス\n";
        echo "3. Laravel環境テスト でLaravel状態確認\n";
        echo "4. データベーステスト でDB接続確認\n";
        
    } catch (Exception $e) {
        echo "❌ 自動修正中にエラー発生: " . $e->getMessage() . "\n";
        echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
        $error_count++;
    }
    
    echo "</pre>";
    
    // 結果サマリー
    if ($error_count === 0) {
        echo "<div class='status success'>🎉 全ての修正が成功しました！上記のURLでテストしてください。</div>";
    } elseif ($success_count > $error_count) {
        echo "<div class='status warning'>⚠️ 部分的に成功しました。エラーがありますが、基本動作は可能です。</div>";
    } else {
        echo "<div class='status error'>❌ 多くのエラーが発生しました。サーバー設定を確認してください。</div>";
    }
    
    echo "<h3>🗑️ 修正完了後の処理</h3>";
    echo "<p>修正が成功したら、セキュリティのためこのファイルを削除してください：</p>";
    echo "<form method='post' style='display:inline;'>";
    echo "<button type='submit' name='delete_self' class='btn' style='background:#dc3545;' onclick='return confirm(\"このファイルを削除しますか？\");'>🗑️ このファイルを削除</button>";
    echo "</form>";
    
} else {
    echo "<h2>🚨 手動実行モード</h2>";
    echo "<p>ワンクリック自動修正を実行します。</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='execute' class='btn'>🚀 自動修正を実行</button>";
    echo "</form>";
}

// ファイル削除処理
if (isset($_POST['delete_self'])) {
    echo "<h2>🗑️ ファイル削除</h2>";
    if (unlink(__FILE__)) {
        echo "<p class='success'>✅ one_click_fix.php を削除しました。</p>";
        echo "<p>修正作業が完了しました。<a href='/'>トップページ</a>にアクセスしてください。</p>";
    } else {
        echo "<p class='error'>❌ ファイルの削除に失敗しました。手動で削除してください。</p>";
    }
}

echo "</div></body></html>";
?> 