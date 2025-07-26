<?php
// 本番環境 APP_KEY 緊急修正スクリプト
// 特定のAPP_KEYで.envファイルを修正

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔑 本番環境 APP_KEY 修正</h1>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";

// 正しいAPP_KEY（Laravel標準32バイト）
$correct_app_key = 'base64:qWKe8uFO6ALKta3hmDb42Bsi/gHppLn4S/MFQ4ZJiTg=';

if (isset($_POST['fix_now'])) {
    echo "<h2>🚀 APP_KEY修正実行中...</h2>";
    
    try {
        // .envファイルの確認
        if (!file_exists('.env')) {
            echo "<p>❌ .envファイルが見つかりません</p>";
            exit;
        }
        
        // バックアップ作成
        $backup_file = '.env.backup.' . date('Ymd_His');
        copy('.env', $backup_file);
        echo "<p>✅ バックアップ作成: {$backup_file}</p>";
        
        // .env内容を読み込み
        $env_content = file_get_contents('.env');
        
        // 現在のAPP_KEYを表示
        if (preg_match('/APP_KEY=(.*)/', $env_content, $matches)) {
            $current_key = trim($matches[1]);
            echo "<p>現在のAPP_KEY: " . htmlspecialchars($current_key) . "</p>";
        }
        
        // APP_KEYを正しい値に置換
        if (preg_match('/APP_KEY=(.*)/', $env_content)) {
            $new_env_content = preg_replace('/APP_KEY=(.*)/', 'APP_KEY=' . $correct_app_key, $env_content);
        } else {
            // APP_KEYが存在しない場合は追加
            $new_env_content = $env_content . "\nAPP_KEY=" . $correct_app_key . "\n";
        }
        
        // ファイルに書き込み
        file_put_contents('.env', $new_env_content);
        echo "<p>✅ .envファイルを更新しました</p>";
        echo "<p>新しいAPP_KEY: " . htmlspecialchars($correct_app_key) . "</p>";
        
        // キャッシュクリア
        $cache_files = [
            'bootstrap/cache/config.php',
            'bootstrap/cache/routes.php',
            'bootstrap/cache/services.php'
        ];
        
        foreach ($cache_files as $cache_file) {
            if (file_exists($cache_file)) {
                unlink($cache_file);
                echo "<p>✅ {$cache_file} を削除</p>";
            }
        }
        
        // キャッシュディレクトリのクリア
        $cache_dirs = [
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views'
        ];
        
        foreach ($cache_dirs as $cache_dir) {
            if (is_dir($cache_dir)) {
                $files = glob($cache_dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                echo "<p>✅ {$cache_dir} をクリア</p>";
            }
        }
        
        echo "<h2>🎉 修正完了！</h2>";
        echo "<p><a href='https://koutei.kiryu-factory.com/' target='_blank'>メインサイト</a>にアクセスして動作確認してください。</p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<h2>🔍 現在の問題</h2>";
    echo "<p>Laravel ログから以下のエラーが確認されています：</p>";
    echo "<ul>";
    echo "<li>❌ MissingAppKeyException: アプリケーション暗号化キーが指定されていない</li>";
    echo "<li>❌ Unsupported cipher or incorrect key length: 暗号化方式がサポートされていない</li>";
    echo "</ul>";
    
    echo "<h3>📄 現在の.env状況:</h3>";
    if (file_exists('.env')) {
        $env_content = file_get_contents('.env');
        if (preg_match('/APP_KEY=(.*)/', $env_content, $matches)) {
            $current_key = trim($matches[1]);
            echo "<pre>APP_KEY=" . htmlspecialchars($current_key) . "</pre>";
            
            if ($current_key === 'base64:KOUTEI_SYSTEM_PRODUCTION_KEY_2025') {
                echo "<p>❌ 問題: カスタムキーが設定されていますが、Laravel標準形式ではありません</p>";
            }
        }
    }
    
    echo "<h2>🔧 修正内容</h2>";
    echo "<p>正しいLaravel標準の32バイトAPP_KEYに修正します：</p>";
    echo "<pre>" . htmlspecialchars($correct_app_key) . "</pre>";
    
    echo "<div style='margin:20px 0; padding:15px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:5px;'>";
    echo "<h3>⚠️ 注意事項</h3>";
    echo "<p>APP_KEYを変更すると、既存の暗号化されたセッションデータは無効になります。</p>";
    echo "<p>ユーザーは再ログインが必要になる可能性があります。</p>";
    echo "</div>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_now' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🚨 今すぐ修正する</button>";
    echo "</form>";
}
?> 