<?php
// Laravel 完全起動修正スクリプト
// 本番環境でLaravelを起動させるための包括的修正

// セキュリティ：本番環境でのみ実行を許可
$allowed_hosts = ['koutei.kiryu-factory.com', 'www.koutei.kiryu-factory.com'];
$current_host = $_SERVER['HTTP_HOST'] ?? '';

if (!in_array($current_host, $allowed_hosts) && $current_host !== 'localhost') {
    die('このスクリプトは本番環境でのみ実行できます。');
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 完全起動修正スクリプト</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
        .btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; margin: 10px 5px; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel 完全起動修正スクリプト</h1>
        <p><strong>サイト:</strong> <?= htmlspecialchars($current_host) ?></p>
        <p><strong>実行時刻:</strong> <?= date('Y-m-d H:i:s') ?></p>
        
        <?php if (isset($_POST['execute'])): ?>
            <h2>🔧 Laravel 起動修正実行結果</h2>
            <pre><?php

try {
    echo "=== Laravel 完全起動修正開始 ===\n\n";
    
    // 1. 環境情報の確認
    echo "1. 環境情報の確認...\n";
    echo "PHP バージョン: " . phpversion() . "\n";
    echo "現在のディレクトリ: " . __DIR__ . "\n";
    echo "ドキュメントルート: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "\n";
    
    // 2. Laravel ディレクトリ構造の確認
    echo "2. Laravel ディレクトリ構造の確認...\n";
    $laravel_dirs = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'vendor'];
    $missing_dirs = [];
    
    foreach ($laravel_dirs as $dir) {
        if (is_dir($dir)) {
            echo "✅ {$dir}/ ディレクトリが存在します\n";
        } else {
            echo "❌ {$dir}/ ディレクトリが見つかりません\n";
            $missing_dirs[] = $dir;
        }
    }
    echo "\n";
    
    // 3. .env ファイルの確認と作成
    echo "3. .env ファイルの確認と作成...\n";
    if (!file_exists('.env')) {
        $env_content = "APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://koutei.kiryu-factory.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=factory0328_wp2
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
MIX_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";
        
        if (file_put_contents('.env', $env_content)) {
            echo "✅ .env ファイルを作成しました\n";
        } else {
            echo "❌ .env ファイルの作成に失敗しました\n";
        }
    } else {
        echo "✅ .env ファイルが存在します\n";
    }
    echo "\n";
    
    // 4. アプリケーションキーの生成
    echo "4. アプリケーションキーの確認...\n";
    if (file_exists('.env')) {
        $env_content = file_get_contents('.env');
        if (strpos($env_content, 'APP_KEY=') !== false && strpos($env_content, 'APP_KEY=base64:') === false) {
            // 簡単なキー生成
            $key = 'base64:' . base64_encode(random_bytes(32));
            $env_content = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $env_content);
            file_put_contents('.env', $env_content);
            echo "✅ アプリケーションキーを生成しました: $key\n";
        } else {
            echo "✅ アプリケーションキーが設定済みです\n";
        }
    }
    echo "\n";
    
    // 5. storage と bootstrap/cache ディレクトリの権限設定
    echo "5. ディレクトリ権限の設定...\n";
    $writable_dirs = ['storage', 'bootstrap/cache'];
    foreach ($writable_dirs as $dir) {
        if (is_dir($dir)) {
            if (chmod($dir, 0755)) {
                echo "✅ {$dir} の権限を設定しました\n";
            } else {
                echo "⚠️ {$dir} の権限設定に失敗しました\n";
            }
        } else {
            echo "❌ {$dir} ディレクトリが見つかりません\n";
        }
    }
    echo "\n";
    
    // 6. データベース接続テスト
    echo "6. データベース接続テスト...\n";
    $possible_databases = [
        'factory0328_wp2',
        'koutei',
        'laravel',
        'kiryu_factory'
    ];
    
    $pdo = null;
    $connected_db = '';
    
    foreach ($possible_databases as $db) {
        try {
            $dsn = "mysql:host=localhost;dbname=$db;charset=utf8mb4";
            $pdo = new PDO($dsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $connected_db = $db;
            echo "✅ データベース '$db' に接続成功\n";
            break;
        } catch (PDOException $e) {
            echo "⚠️ データベース '$db' への接続失敗\n";
        }
    }
    
    if (!$pdo) {
        echo "❌ すべてのデータベースへの接続に失敗しました\n";
    }
    echo "\n";
    
    // 7. データベース修正（前回のスクリプトと同じ）
    if ($pdo) {
        echo "7. データベース構造の修正...\n";
        
        // usersテーブルの修正
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM users");
            $stmt->execute();
            $columns = $stmt->fetchAll();
            $existing_columns = array_column($columns, 'Field');
            
            if (!in_array('password', $existing_columns)) {
                $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255) NULL");
                echo "✅ usersテーブルにpasswordカラムを追加\n";
            }
            
            if (!in_array('user_id', $existing_columns)) {
                $pdo->exec("ALTER TABLE users ADD COLUMN user_id VARCHAR(255) UNIQUE NULL");
                echo "✅ usersテーブルにuser_idカラムを追加\n";
            }
            
            if (!in_array('role', $existing_columns)) {
                $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user'");
                echo "✅ usersテーブルにroleカラムを追加\n";
            }
        } catch (Exception $e) {
            echo "⚠️ usersテーブル修正エラー: " . $e->getMessage() . "\n";
        }
        
        // wp_wqorders_editableテーブルの修正
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM wp_wqorders_editable");
            $stmt->execute();
            $columns = $stmt->fetchAll();
            $existing_columns = array_column($columns, 'Field');
            
            $required_columns = [
                'notes' => 'TEXT NULL',
                'last_updated' => 'INT(11) NULL',
                'order_handler_id' => 'INT(11) NULL'
            ];
            
            foreach ($required_columns as $column => $definition) {
                if (!in_array($column, $existing_columns)) {
                    $pdo->exec("ALTER TABLE wp_wqorders_editable ADD COLUMN $column $definition");
                    echo "✅ wp_wqorders_editableテーブルに{$column}カラムを追加\n";
                }
            }
        } catch (Exception $e) {
            echo "⚠️ wp_wqorders_editableテーブル修正エラー: " . $e->getMessage() . "\n";
        }
        
        // パスワード設定
        try {
            $stmt = $pdo->prepare("SELECT id, user_id FROM users WHERE password IS NULL OR password = ''");
            $stmt->execute();
            $users = $stmt->fetchAll();
            
            foreach ($users as $user) {
                $password = $user['user_id'] === 'admin' ? 'password' : 'employee123';
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user['id']]);
                echo "✅ {$user['user_id']}のパスワードを設定\n";
            }
        } catch (Exception $e) {
            echo "⚠️ パスワード設定エラー: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
    
    // 8. 最終確認
    echo "8. 最終確認...\n";
    echo "✅ PHP実行可能: " . (function_exists('exec') ? 'Yes' : 'No') . "\n";
    echo "✅ ファイル書き込み権限: " . (is_writable('.') ? 'Yes' : 'No') . "\n";
    echo "✅ Laravel bootstrap: " . (file_exists('bootstrap/app.php') ? 'Yes' : 'No') . "\n";
    echo "✅ Public index: " . (file_exists('public/index.php') ? 'Yes' : 'No') . "\n";
    
    echo "\n🎉 Laravel起動修正完了！\n";
    echo "\n次のステップ:\n";
    echo "1. https://koutei.kiryu-factory.com にアクセス\n";
    echo "2. エラーが出る場合は、サーバーのエラーログを確認\n";
    echo "3. ログイン: admin / password\n";
    echo "4. このファイルを削除してください\n";
    
} catch (Exception $e) {
    echo "❌ エラー発生: " . $e->getMessage() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
}

            ?></pre>
            
            <div style="margin-top: 20px;">
                <form method="post" onsubmit="return confirm('このファイルを削除しますか？');">
                    <button type="submit" name="delete_self" class="btn btn-danger">🗑️ このファイルを削除</button>
                </form>
            </div>
            
        <?php elseif (isset($_POST['delete_self'])): ?>
            <h2>🗑️ ファイル削除</h2>
            <?php
            if (unlink(__FILE__)) {
                echo '<p class="success">✅ laravel_startup_fix.php を削除しました。</p>';
                echo '<p>修正作業が完了しました。<a href="/">トップページ</a>にアクセスしてください。</p>';
            } else {
                echo '<p class="error">❌ ファイルの削除に失敗しました。手動で削除してください。</p>';
            }
            ?>
        <?php else: ?>
            <div class="step">
                <h2>⚠️ Laravel 起動問題の診断</h2>
                <p><strong>現在の状況:</strong></p>
                <ul>
                    <li>✅ PHP実行環境: <?= phpversion() ?></li>
                    <li>✅ 現在のディレクトリ: <?= __DIR__ ?></li>
                    <li>✅ Laravelプロジェクト: <?= file_exists('artisan') ? '検出' : '未検出' ?></li>
                    <li>✅ .envファイル: <?= file_exists('.env') ? '存在' : '不在' ?></li>
                    <li>✅ vendorディレクトリ: <?= is_dir('vendor') ? '存在' : '不在' ?></li>
                </ul>
            </div>
            
            <div class="warning">
                <p><strong>このスクリプトは以下を実行します:</strong></p>
                <ul>
                    <li>Laravel環境の完全診断</li>
                    <li>.envファイルの作成</li>
                    <li>アプリケーションキーの生成</li>
                    <li>ディレクトリ権限の設定</li>
                    <li>データベース構造の修正</li>
                    <li>ユーザーパスワードの設定</li>
                </ul>
            </div>
            
            <form method="post">
                <button type="submit" name="execute" class="btn" onclick="return confirm('Laravel完全修正を実行しますか？');">
                    🚀 Laravel 完全修正を実行する
                </button>
            </form>
        <?php endif; ?>
        
        <hr style="margin: 30px 0;">
        <p class="info">
            <strong>注意:</strong> 修正完了後は、セキュリティのため必ずこのファイルを削除してください。
        </p>
    </div>
</body>
</html> 