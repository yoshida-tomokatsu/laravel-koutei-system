<?php
// Laravel APP_KEY 緊急修正スクリプト
// 500エラーの原因となっているアプリケーションキー問題を修正

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔑 Laravel APP_KEY 緊急修正</h1>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>実行場所: " . __DIR__ . "</p>";

// 修正を実行するかの確認
if (isset($_POST['fix_app_key'])) {
    echo "<h2>🚀 APP_KEY修正実行中...</h2>";
    
    try {
        // 1. 現在の.envファイルの確認
        $env_file = '.env';
        if (!file_exists($env_file)) {
            echo "<p>❌ .envファイルが見つかりません</p>";
            exit;
        }
        
        // 2. 現在の.envファイルをバックアップ
        $backup_file = '.env.backup.' . date('Ymd_His');
        copy($env_file, $backup_file);
        echo "<p>✅ .envファイルを {$backup_file} にバックアップしました</p>";
        
        // 3. 現在の.env内容を読み込み
        $env_content = file_get_contents($env_file);
        echo "<h3>📄 現在のAPP_KEY設定:</h3>";
        if (preg_match('/APP_KEY=(.*)/', $env_content, $matches)) {
            $current_key = trim($matches[1]);
            echo "<pre>APP_KEY=" . htmlspecialchars($current_key) . "</pre>";
            
            // 問題の分析
            echo "<h3>🔍 問題分析:</h3>";
            if (empty($current_key)) {
                echo "<p>❌ APP_KEYが空です</p>";
            } elseif (!str_starts_with($current_key, 'base64:')) {
                echo "<p>❌ APP_KEYが正しいbase64形式ではありません</p>";
            } else {
                $key_data = base64_decode(substr($current_key, 7));
                $key_length = strlen($key_data);
                echo "<p>🔍 現在のキー長: {$key_length} バイト</p>";
                if ($key_length !== 32) {
                    echo "<p>❌ キー長が不正です（32バイト必要）</p>";
                } else {
                    echo "<p>⚠️ キー長は正しいですが、他の問題があります</p>";
                }
            }
        } else {
            echo "<p>❌ APP_KEY設定が見つかりません</p>";
        }
        
        // 4. 正しいAPP_KEYを生成
        echo "<h3>🔧 新しいAPP_KEY生成中...</h3>";
        
        // Laravel標準の32バイトキーを生成
        $new_key_bytes = random_bytes(32);
        $new_app_key = 'base64:' . base64_encode($new_key_bytes);
        
        echo "<p>✅ 新しいAPP_KEYを生成しました</p>";
        echo "<pre>新しいAPP_KEY=" . htmlspecialchars($new_app_key) . "</pre>";
        
        // 5. .envファイルを更新
        if (preg_match('/APP_KEY=(.*)/', $env_content)) {
            $new_env_content = preg_replace('/APP_KEY=(.*)/', 'APP_KEY=' . $new_app_key, $env_content);
        } else {
            // APP_KEYがない場合は追加
            $new_env_content = $env_content . "\nAPP_KEY=" . $new_app_key . "\n";
        }
        
        // ファイルに書き込み
        file_put_contents($env_file, $new_env_content);
        echo "<p>✅ .envファイルを更新しました</p>";
        
        // 6. 設定キャッシュをクリア（可能な場合）
        echo "<h3>🧹 キャッシュクリア...</h3>";
        $cache_dirs = [
            'bootstrap/cache/config.php',
            'bootstrap/cache/routes.php',
            'bootstrap/cache/services.php',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views'
        ];
        
        foreach ($cache_dirs as $cache_path) {
            if (file_exists($cache_path)) {
                if (is_file($cache_path)) {
                    unlink($cache_path);
                    echo "<p>✅ {$cache_path} を削除しました</p>";
                } elseif (is_dir($cache_path)) {
                    $files = glob($cache_path . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    echo "<p>✅ {$cache_path} 内のファイルを削除しました</p>";
                }
            }
        }
        
        // 7. 最終テスト用のファイルを作成
        $test_script = "<?php\n";
        $test_script .= "// APP_KEY修正後のテストスクリプト\n";
        $test_script .= "echo '<h1>🧪 Laravel起動テスト</h1>';\n";
        $test_script .= "echo '<p>実行時刻: ' . date('Y-m-d H:i:s') . '</p>';\n";
        $test_script .= "\n";
        $test_script .= "try {\n";
        $test_script .= "    if (file_exists('vendor/autoload.php')) {\n";
        $test_script .= "        require 'vendor/autoload.php';\n";
        $test_script .= "        echo '<p>✅ vendor/autoload.php 読み込み成功</p>';\n";
        $test_script .= "        \n";
        $test_script .= "        if (file_exists('bootstrap/app.php')) {\n";
        $test_script .= "            \$app = require_once 'bootstrap/app.php';\n";
        $test_script .= "            echo '<p>✅ Laravel アプリケーション作成成功</p>';\n";
        $test_script .= "            \n";
        $test_script .= "            // 暗号化サービスのテスト\n";
        $test_script .= "            \$encrypter = \$app->make('encrypter');\n";
        $test_script .= "            echo '<p>✅ 暗号化サービス初期化成功</p>';\n";
        $test_script .= "            \n";
        $test_script .= "            // 簡単な暗号化テスト\n";
        $test_script .= "            \$test_data = 'Hello World';\n";
        $test_script .= "            \$encrypted = \$encrypter->encrypt(\$test_data);\n";
        $test_script .= "            \$decrypted = \$encrypter->decrypt(\$encrypted);\n";
        $test_script .= "            \n";
        $test_script .= "            if (\$decrypted === \$test_data) {\n";
        $test_script .= "                echo '<p>✅ 暗号化・復号化テスト成功</p>';\n";
        $test_script .= "                echo '<h2>🎉 APP_KEY修正完了！</h2>';\n";
        $test_script .= "                echo '<p>Laravelアプリケーションは正常に動作するはずです。</p>';\n";
        $test_script .= "            } else {\n";
        $test_script .= "                echo '<p>❌ 暗号化テスト失敗</p>';\n";
        $test_script .= "            }\n";
        $test_script .= "        } else {\n";
        $test_script .= "            echo '<p>❌ bootstrap/app.php が見つかりません</p>';\n";
        $test_script .= "        }\n";
        $test_script .= "    } else {\n";
        $test_script .= "        echo '<p>❌ vendor/autoload.php が見つかりません</p>';\n";
        $test_script .= "    }\n";
        $test_script .= "} catch (Exception \$e) {\n";
        $test_script .= "    echo '<h3>❌ エラーが発生しました</h3>';\n";
        $test_script .= "    echo '<p><strong>エラーメッセージ:</strong></p>';\n";
        $test_script .= "    echo '<pre>' . htmlspecialchars(\$e->getMessage()) . '</pre>';\n";
        $test_script .= "    echo '<p><strong>ファイル:</strong> ' . \$e->getFile() . '</p>';\n";
        $test_script .= "    echo '<p><strong>行:</strong> ' . \$e->getLine() . '</p>';\n";
        $test_script .= "}\n";
        $test_script .= "?>";
        
        file_put_contents('test_app_key.php', $test_script);
        echo "<p>✅ テストスクリプト test_app_key.php を作成しました</p>";
        
        echo "<h2>🎉 修正完了！</h2>";
        echo "<h3>📋 次のステップ:</h3>";
        echo "<ol>";
        echo "<li><a href='test_app_key.php' target='_blank'>test_app_key.php</a> でテスト実行</li>";
        echo "<li><a href='https://koutei.kiryu-factory.com/' target='_blank'>メインサイト</a> にアクセスして動作確認</li>";
        echo "<li>問題が解決しない場合は、サーバーの再起動を検討</li>";
        echo "</ol>";
        
        echo "<h3>📄 生成されたファイル:</h3>";
        echo "<ul>";
        echo "<li>{$backup_file} (.envのバックアップ)</li>";
        echo "<li>test_app_key.php (テストスクリプト)</li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
} else {
    // 現在の状況を診断
    echo "<h2>🔍 現在の問題診断</h2>";
    echo "<p>Laravelログから以下の問題が確認されました：</p>";
    echo "<ul>";
    echo "<li>❌ <strong>MissingAppKeyException</strong>: アプリケーション暗号化キーが指定されていない</li>";
    echo "<li>❌ <strong>Unsupported cipher or incorrect key length</strong>: 暗号化方式がサポートされていない、またはキーの長さが不正</li>";
    echo "</ul>";
    
    // 現在の.env状況確認
    echo "<h3>📄 現在の.env状況:</h3>";
    if (file_exists('.env')) {
        $env_content = file_get_contents('.env');
        if (preg_match('/APP_KEY=(.*)/', $env_content, $matches)) {
            $current_key = trim($matches[1]);
            echo "<pre>APP_KEY=" . htmlspecialchars($current_key) . "</pre>";
            
            echo "<h3>🔍 問題の原因:</h3>";
            if (empty($current_key)) {
                echo "<p>❌ APP_KEYが空です</p>";
            } elseif ($current_key === 'base64:KOUTEI_SYSTEM_PRODUCTION_KEY_2025') {
                echo "<p>❌ カスタムキーが設定されていますが、Laravel標準形式ではありません</p>";
                echo "<p>Laravelは32バイトの正確なキーを要求します</p>";
            } elseif (!str_starts_with($current_key, 'base64:')) {
                echo "<p>❌ APP_KEYが正しいbase64形式ではありません</p>";
            } else {
                echo "<p>⚠️ キー形式は正しいようですが、内容に問題があります</p>";
            }
        } else {
            echo "<p>❌ APP_KEY設定が見つかりません</p>";
        }
    } else {
        echo "<p>❌ .envファイルが見つかりません</p>";
    }
    
    echo "<h2>🔧 修正内容</h2>";
    echo "<p>このスクリプトは以下の修正を行います：</p>";
    echo "<ul>";
    echo "<li>✅ 現在の.envファイルをバックアップ</li>";
    echo "<li>✅ Laravel標準の32バイトAPP_KEYを生成</li>";
    echo "<li>✅ .envファイルのAPP_KEYを更新</li>";
    echo "<li>✅ 設定キャッシュをクリア</li>";
    echo "<li>✅ テストスクリプトを作成</li>";
    echo "</ul>";
    
    echo "<div style='margin:20px 0; padding:15px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:5px;'>";
    echo "<h3>⚠️ 重要な注意</h3>";
    echo "<p>APP_KEYを変更すると、既存の暗号化されたデータ（セッション、パスワードリセットトークンなど）は無効になります。</p>";
    echo "<p>本番環境では、この操作により一時的にユーザーがログアウトされる可能性があります。</p>";
    echo "</div>";
    
    echo "<form method='post' style='margin:20px 0;'>";
    echo "<button type='submit' name='fix_app_key' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🚨 緊急修正を実行する</button>";
    echo "</form>";
}
?> 