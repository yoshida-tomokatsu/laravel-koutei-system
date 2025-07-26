<?php
// 本番環境専用Laravel修正スクリプト
// 実際の本番環境設定に基づく修正

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
    <title>本番環境Laravel修正スクリプト</title>
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
        <h1>🔧 本番環境Laravel修正スクリプト</h1>
        <p><strong>サイト:</strong> <?= htmlspecialchars($current_host) ?></p>
        <p><strong>実行時刻:</strong> <?= date('Y-m-d H:i:s') ?></p>
        
        <?php if (isset($_POST['execute'])): ?>
            <h2>🚀 本番環境修正実行結果</h2>
            <pre><?php

try {
    echo "=== 本番環境Laravel修正開始 ===\n\n";
    
    // 1. 環境情報の確認
    echo "1. 環境情報の確認...\n";
    echo "PHP バージョン: " . phpversion() . "\n";
    echo "現在のディレクトリ: " . __DIR__ . "\n";
    echo "ドキュメントルート: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "\n";
    
    // 2. Laravel環境の確認
    echo "2. Laravel環境の確認...\n";
    $laravel_files = [
        'artisan' => 'Laravel Artisan',
        '.env' => '環境設定ファイル',
        'bootstrap/app.php' => 'Bootstrap',
        'public/index.php' => 'Public Index',
        'vendor/autoload.php' => 'Composer Autoload'
    ];
    
    foreach ($laravel_files as $file => $description) {
        if (file_exists($file)) {
            echo "✅ {$description}: 存在\n";
        } else {
            echo "❌ {$description}: 不在\n";
        }
    }
    echo "\n";
    
    // 3. データベース接続テスト（本番環境設定）
    echo "3. データベース接続テスト...\n";
    try {
        $dsn = "mysql:host=localhost;dbname=factory0328_wp2;charset=utf8mb4";
        $pdo = new PDO($dsn, 'factory0328_wp2', 'ctwjr3mmf5', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        echo "✅ データベース 'factory0328_wp2' に接続成功\n";
    } catch (PDOException $e) {
        echo "❌ データベース接続失敗: " . $e->getMessage() . "\n";
        $pdo = null;
    }
    echo "\n";
    
    // 4. 必要なテーブルの確認
    if ($pdo) {
        echo "4. 必要なテーブルの確認...\n";
        $required_tables = ['users', 'wp_wqorders_editable', 'migrations'];
        
        foreach ($required_tables as $table) {
            try {
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                if ($stmt->fetch()) {
                    echo "✅ テーブル '{$table}' が存在します\n";
                } else {
                    echo "❌ テーブル '{$table}' が見つかりません\n";
                }
            } catch (Exception $e) {
                echo "❌ テーブル確認エラー: " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
        
        // 5. usersテーブルの構造確認と修正
        echo "5. usersテーブルの構造確認と修正...\n";
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM users");
            $stmt->execute();
            $columns = $stmt->fetchAll();
            $existing_columns = array_column($columns, 'Field');
            
            echo "既存のカラム: " . implode(', ', $existing_columns) . "\n";
            
            // 必要なカラムの追加
            $required_columns = [
                'password' => 'VARCHAR(255) NULL',
                'user_id' => 'VARCHAR(255) UNIQUE NULL',
                'role' => 'VARCHAR(50) DEFAULT \'user\''
            ];
            
            foreach ($required_columns as $column => $definition) {
                if (!in_array($column, $existing_columns)) {
                    $pdo->exec("ALTER TABLE users ADD COLUMN {$column} {$definition}");
                    echo "✅ usersテーブルに{$column}カラムを追加しました\n";
                } else {
                    echo "✅ {$column}カラムは既に存在します\n";
                }
            }
        } catch (Exception $e) {
            echo "⚠️ usersテーブル修正エラー: " . $e->getMessage() . "\n";
        }
        echo "\n";
        
        // 6. wp_wqorders_editableテーブルの構造確認と修正
        echo "6. wp_wqorders_editableテーブルの構造確認と修正...\n";
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM wp_wqorders_editable");
            $stmt->execute();
            $columns = $stmt->fetchAll();
            $existing_columns = array_column($columns, 'Field');
            
            $required_columns = [
                'notes' => 'TEXT NULL',
                'last_updated' => 'INT(11) NULL',
                'order_handler_id' => 'INT(11) NULL',
                'image_sent_date' => 'DATE NULL',
                'payment_method_id' => 'INT(11) NULL',
                'payment_completed_date' => 'DATE NULL',
                'print_factory_id' => 'INT(11) NULL',
                'print_request_date' => 'DATE NULL',
                'print_deadline' => 'DATE NULL',
                'sewing_factory_id' => 'INT(11) NULL',
                'sewing_request_date' => 'DATE NULL',
                'sewing_deadline' => 'DATE NULL',
                'quality_check_date' => 'DATE NULL',
                'shipping_date' => 'DATE NULL'
            ];
            
            foreach ($required_columns as $column => $definition) {
                if (!in_array($column, $existing_columns)) {
                    $pdo->exec("ALTER TABLE wp_wqorders_editable ADD COLUMN {$column} {$definition}");
                    echo "✅ wp_wqorders_editableテーブルに{$column}カラムを追加しました\n";
                } else {
                    echo "✅ {$column}カラムは既に存在します\n";
                }
            }
        } catch (Exception $e) {
            echo "⚠️ wp_wqorders_editableテーブル修正エラー: " . $e->getMessage() . "\n";
        }
        echo "\n";
        
        // 7. ユーザーパスワードの設定
        echo "7. ユーザーパスワードの設定...\n";
        try {
            // まず既存ユーザーを確認
            $stmt = $pdo->prepare("SELECT id, user_id, name, password FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll();
            
            echo "既存ユーザー数: " . count($users) . "\n";
            
            foreach ($users as $user) {
                if (empty($user['password'])) {
                    $password = $user['user_id'] === 'admin' ? 'password' : 'employee123';
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed, $user['id']]);
                    echo "✅ {$user['user_id']}のパスワードを設定しました\n";
                } else {
                    echo "✅ {$user['user_id']}のパスワードは設定済みです\n";
                }
            }
            
            // 管理者ユーザーが存在しない場合は作成
            $admin_exists = false;
            foreach ($users as $user) {
                if ($user['user_id'] === 'admin') {
                    $admin_exists = true;
                    break;
                }
            }
            
            if (!$admin_exists) {
                $stmt = $pdo->prepare("INSERT INTO users (user_id, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    'admin',
                    password_hash('password', PASSWORD_DEFAULT),
                    '管理者',
                    'admin@kiryu-factory.com',
                    'admin'
                ]);
                echo "✅ 管理者ユーザーを作成しました\n";
            }
        } catch (Exception $e) {
            echo "⚠️ ユーザーパスワード設定エラー: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
    // 8. キャッシュクリア（可能な場合）
    echo "8. キャッシュクリア...\n";
    $cache_dirs = [
        'bootstrap/cache' => 'Bootstrap Cache',
        'storage/framework/cache' => 'Framework Cache',
        'storage/framework/sessions' => 'Sessions',
        'storage/framework/views' => 'Compiled Views'
    ];
    
    foreach ($cache_dirs as $dir => $description) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            $cleared = 0;
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    if (unlink($file)) {
                        $cleared++;
                    }
                }
            }
            echo "✅ {$description}: {$cleared}個のファイルをクリアしました\n";
        } else {
            echo "⚠️ {$description}: ディレクトリが見つかりません\n";
        }
    }
    echo "\n";
    
    // 9. 権限設定
    echo "9. ディレクトリ権限の設定...\n";
    $writable_dirs = ['storage', 'bootstrap/cache'];
    foreach ($writable_dirs as $dir) {
        if (is_dir($dir)) {
            if (chmod($dir, 0755)) {
                echo "✅ {$dir} の権限を755に設定しました\n";
            } else {
                echo "⚠️ {$dir} の権限設定に失敗しました\n";
            }
        }
    }
    echo "\n";
    
    // 10. 最終確認
    echo "10. 最終確認...\n";
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $user_count = $stmt->fetch()['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM wp_wqorders_editable");
        $stmt->execute();
        $order_count = $stmt->fetch()['count'];
        
        echo "✅ ユーザー数: {$user_count}\n";
        echo "✅ 注文数: {$order_count}\n";
    }
    
    echo "✅ Laravel環境: " . (file_exists('artisan') ? '正常' : '異常') . "\n";
    echo "✅ .envファイル: " . (file_exists('.env') ? '存在' : '不在') . "\n";
    echo "✅ Vendorディレクトリ: " . (is_dir('vendor') ? '存在' : '不在') . "\n";
    
    echo "\n🎉 本番環境修正完了！\n";
    echo "\n次のステップ:\n";
    echo "1. https://koutei.kiryu-factory.com にアクセス\n";
    echo "2. ログイン: admin / password\n";
    echo "3. エラーが継続する場合は、Webサーバーのエラーログを確認\n";
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
                echo '<p class="success">✅ production_env_fix.php を削除しました。</p>';
                echo '<p>修正作業が完了しました。<a href="/">トップページ</a>にアクセスしてください。</p>';
            } else {
                echo '<p class="error">❌ ファイルの削除に失敗しました。手動で削除してください。</p>';
            }
            ?>
        <?php else: ?>
            <div class="step">
                <h2>⚠️ 本番環境Laravel修正</h2>
                <p><strong>現在の状況:</strong></p>
                <ul>
                    <li>✅ PHP実行環境: <?= phpversion() ?></li>
                    <li>✅ 現在のディレクトリ: <?= __DIR__ ?></li>
                    <li>✅ .envファイル: <?= file_exists('.env') ? '存在' : '不在' ?></li>
                    <li>✅ vendorディレクトリ: <?= is_dir('vendor') ? '存在' : '不在' ?></li>
                    <li>✅ Laravel Artisan: <?= file_exists('artisan') ? '存在' : '不在' ?></li>
                </ul>
            </div>
            
            <div class="warning">
                <p><strong>このスクリプトは以下を実行します:</strong></p>
                <ul>
                    <li>本番データベース(factory0328_wp2)への接続テスト</li>
                    <li>必要なテーブル構造の確認と修正</li>
                    <li>usersテーブルにpassword, user_id, roleカラム追加</li>
                    <li>wp_wqorders_editableテーブルに必要カラム追加</li>
                    <li>管理者ユーザーの作成・パスワード設定</li>
                    <li>キャッシュファイルのクリア</li>
                    <li>ディレクトリ権限の設定</li>
                </ul>
            </div>
            
            <form method="post">
                <button type="submit" name="execute" class="btn" onclick="return confirm('本番環境修正を実行しますか？');">
                    🚀 本番環境修正を実行する
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