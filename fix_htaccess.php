<?php
// .htaccess問題の緊急修正スクリプト

echo "<h1>🔧 .htaccess問題修正スクリプト</h1>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";

if (isset($_POST['fix'])) {
    echo "<h2>🚀 修正実行中...</h2>";
    
    try {
        // 1. 現在の.htaccessをバックアップ
        if (file_exists('.htaccess')) {
            copy('.htaccess', '.htaccess.backup');
            echo "<p>✅ 既存.htaccessをバックアップしました</p>";
        }
        
        // 2. publicディレクトリの確認
        if (!is_dir('public')) {
            mkdir('public', 0755, true);
            echo "<p>✅ publicディレクトリを作成しました</p>";
        } else {
            echo "<p>✅ publicディレクトリは既に存在します</p>";
        }
        
        // 3. public/index.phpの作成
        $public_index = "<?php\n";
        $public_index .= "// Laravel public/index.php\n";
        $public_index .= "define('LARAVEL_START', microtime(true));\n\n";
        $public_index .= "// Laravelのオートローダー\n";
        $public_index .= "if (file_exists(__DIR__.'/../vendor/autoload.php')) {\n";
        $public_index .= "    require __DIR__.'/../vendor/autoload.php';\n";
        $public_index .= "} else {\n";
        $public_index .= "    echo 'vendor/autoload.php が見つかりません。composer install を実行してください。';\n";
        $public_index .= "    exit(1);\n";
        $public_index .= "}\n\n";
        $public_index .= "// Laravelアプリケーションの起動\n";
        $public_index .= "if (file_exists(__DIR__.'/../bootstrap/app.php')) {\n";
        $public_index .= "    \$app = require_once __DIR__.'/../bootstrap/app.php';\n";
        $public_index .= "    \$kernel = \$app->make(Illuminate\\Contracts\\Http\\Kernel::class);\n";
        $public_index .= "    \$response = \$kernel->handle(\n";
        $public_index .= "        \$request = Illuminate\\Http\\Request::capture()\n";
        $public_index .= "    );\n";
        $public_index .= "    \$response->send();\n";
        $public_index .= "    \$kernel->terminate(\$request, \$response);\n";
        $public_index .= "} else {\n";
        $public_index .= "    echo 'bootstrap/app.php が見つかりません。Laravelプロジェクトの構造を確認してください。';\n";
        $public_index .= "}\n";
        
        file_put_contents('public/index.php', $public_index);
        echo "<p>✅ public/index.php を作成しました</p>";
        
        // 4. 新しい.htaccessの作成（より安全な設定）
        $new_htaccess = "<IfModule mod_rewrite.c>\n";
        $new_htaccess .= "    RewriteEngine On\n";
        $new_htaccess .= "    \n";
        $new_htaccess .= "    # publicディレクトリが存在する場合はそちらに転送\n";
        $new_htaccess .= "    RewriteCond %{REQUEST_FILENAME} !-f\n";
        $new_htaccess .= "    RewriteCond %{REQUEST_FILENAME} !-d\n";
        $new_htaccess .= "    RewriteRule ^(.*)$ public/\$1 [L]\n";
        $new_htaccess .= "</IfModule>\n";
        
        file_put_contents('.htaccess', $new_htaccess);
        echo "<p>✅ 新しい.htaccessを作成しました</p>";
        
        // 5. 代替案：ルートindex.phpも更新
        $root_index = "<?php\n";
        $root_index .= "// ルート用index.php - publicディレクトリへのフォールバック\n";
        $root_index .= "if (file_exists(__DIR__.'/public/index.php')) {\n";
        $root_index .= "    // publicディレクトリのindex.phpを実行\n";
        $root_index .= "    chdir(__DIR__.'/public');\n";
        $root_index .= "    require __DIR__.'/public/index.php';\n";
        $root_index .= "} else {\n";
        $root_index .= "    // 直接Laravel起動を試行\n";
        $root_index .= "    echo '<h1>Laravel起動テスト</h1>';\n";
        $root_index .= "    if (file_exists(__DIR__.'/vendor/autoload.php')) {\n";
        $root_index .= "        require __DIR__.'/vendor/autoload.php';\n";
        $root_index .= "        if (file_exists(__DIR__.'/bootstrap/app.php')) {\n";
        $root_index .= "            \$app = require_once __DIR__.'/bootstrap/app.php';\n";
        $root_index .= "            \$kernel = \$app->make(Illuminate\\Contracts\\Http\\Kernel::class);\n";
        $root_index .= "            \$response = \$kernel->handle(\n";
        $root_index .= "                \$request = Illuminate\\Http\\Request::capture()\n";
        $root_index .= "            );\n";
        $root_index .= "            \$response->send();\n";
        $root_index .= "            \$kernel->terminate(\$request, \$response);\n";
        $root_index .= "        } else {\n";
        $root_index .= "            echo 'bootstrap/app.php が見つかりません';\n";
        $root_index .= "        }\n";
        $root_index .= "    } else {\n";
        $root_index .= "        echo 'vendor/autoload.php が見つかりません';\n";
        $root_index .= "    }\n";
        $root_index .= "}\n";
        
        file_put_contents('index.php', $root_index);
        echo "<p>✅ ルートindex.phpを更新しました</p>";
        
        echo "<h3>🎉 修正完了！</h3>";
        echo "<p><strong>テスト方法:</strong></p>";
        echo "<ol>";
        echo "<li><a href='https://koutei.kiryu-factory.com/' target='_blank'>https://koutei.kiryu-factory.com/</a> にアクセス</li>";
        echo "<li><a href='https://koutei.kiryu-factory.com/public/' target='_blank'>https://koutei.kiryu-factory.com/public/</a> にもアクセス</li>";
        echo "</ol>";
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ エラー: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<h2>🚨 問題の説明</h2>";
    echo "<p>現在の.htaccessは全てのリクエストを <code>public/</code> ディレクトリに転送していますが、</p>";
    echo "<p><code>public/index.php</code> が正しく設定されていないため404エラーが発生しています。</p>";
    
    echo "<h3>🔧 修正内容</h3>";
    echo "<ul>";
    echo "<li>✅ publicディレクトリの作成</li>";
    echo "<li>✅ public/index.php の正しい作成</li>";
    echo "<li>✅ .htaccess の安全な設定</li>";
    echo "<li>✅ ルートindex.php のフォールバック設定</li>";
    echo "</ul>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='fix' style='background:#28a745;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🚀 .htaccess問題を修正する</button>";
    echo "</form>";
}
?> 