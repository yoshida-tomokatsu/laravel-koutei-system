<?php
// 本番環境Web修正スクリプト
// ブラウザで https://koutei.kiryu-factory.com/web_fix.php にアクセスして実行

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
    <title>本番環境修正スクリプト</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 本番環境修正スクリプト</h1>
        <p><strong>サイト:</strong> <?= htmlspecialchars($current_host) ?></p>
        <p><strong>実行時刻:</strong> <?= date('Y-m-d H:i:s') ?></p>
        
        <?php if (isset($_POST['execute'])): ?>
            <h2>🚀 修正実行結果</h2>
            <pre><?php
            
try {
    echo "=== 本番環境修正開始 ===\n\n";
    
    // Laravel環境の初期化
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "✅ Composer autoload found\n";
    } else {
        throw new Exception("❌ vendor/autoload.php not found");
    }
    
    if (file_exists('bootstrap/app.php')) {
        $app = require_once 'bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        echo "✅ Laravel application bootstrapped\n\n";
    } else {
        throw new Exception("❌ bootstrap/app.php not found");
    }
    
         // 必要なクラスを使用
     
     // 1. データベース接続テスト
     echo "1. データベース接続テスト...\n";
     try {
         $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "✅ データベース接続成功\n\n";
    } catch (Exception $e) {
        throw new Exception("❌ データベース接続失敗: " . $e->getMessage());
    }
    
    // 2. usersテーブルの修正
    echo "2. usersテーブルの構造確認と修正...\n";
    
    // passwordカラムの追加
    if (!Schema::hasColumn('users', 'password')) {
        DB::statement('ALTER TABLE users ADD COLUMN password VARCHAR(255) NULL');
        echo "✅ passwordカラムを追加しました\n";
    } else {
        echo "✅ passwordカラムは既に存在します\n";
    }
    
    // user_idカラムの追加
    if (!Schema::hasColumn('users', 'user_id')) {
        DB::statement('ALTER TABLE users ADD COLUMN user_id VARCHAR(255) UNIQUE NULL');
        echo "✅ user_idカラムを追加しました\n";
    } else {
        echo "✅ user_idカラムは既に存在します\n";
    }
    
    // roleカラムの追加
    if (!Schema::hasColumn('users', 'role')) {
        DB::statement('ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT \'user\'');
        echo "✅ roleカラムを追加しました\n";
    } else {
        echo "✅ roleカラムは既に存在します\n";
    }
    
    echo "\n";
    
    // 3. wp_wqorders_editableテーブルの修正
    echo "3. wp_wqorders_editableテーブルの構造確認と修正...\n";
    
    $requiredColumns = [
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
    
    foreach ($requiredColumns as $column => $definition) {
        if (!Schema::hasColumn('wp_wqorders_editable', $column)) {
            DB::statement("ALTER TABLE wp_wqorders_editable ADD COLUMN {$column} {$definition}");
            echo "✅ {$column}カラムを追加しました\n";
        } else {
            echo "✅ {$column}カラムは既に存在します\n";
        }
    }
    
    echo "\n";
    
    // 4. ユーザーパスワードの設定
    echo "4. ユーザーパスワードの設定...\n";
    
    $usersWithoutPassword = DB::table('users')
        ->whereNull('password')
        ->orWhere('password', '')
        ->get();
    
    if ($usersWithoutPassword->count() > 0) {
        foreach ($usersWithoutPassword as $user) {
            $password = $user->user_id === 'admin' ? 'password' : 'employee123';
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => Hash::make($password)]);
            echo "✅ {$user->user_id}のパスワードを設定しました\n";
        }
    } else {
        echo "✅ 全ユーザーのパスワードが設定済みです\n";
    }
    
    echo "\n";
    
    // 5. 最終確認
    echo "5. 最終確認...\n";
    $userCount = DB::table('users')->count();
    $orderCount = DB::table('wp_wqorders_editable')->count();
    echo "✅ ユーザー数: {$userCount}\n";
    echo "✅ 注文数: {$orderCount}\n";
    
    echo "\n🎉 修正完了！\n";
    echo "\n次のステップ:\n";
    echo "1. ブラウザで https://koutei.kiryu-factory.com にアクセス\n";
    echo "2. ログイン: admin / password\n";
    echo "3. このファイル(web_fix.php)を削除してください\n";
    
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
                echo '<p class="success">✅ web_fix.php を削除しました。</p>';
                echo '<p>修正作業が完了しました。<a href="/">トップページ</a>にアクセスしてください。</p>';
            } else {
                echo '<p class="error">❌ ファイルの削除に失敗しました。手動で削除してください。</p>';
            }
            ?>
        <?php else: ?>
            <h2>⚠️ 実行前の確認</h2>
            <div class="warning">
                <p><strong>このスクリプトは本番データベースを変更します。</strong></p>
                <ul>
                    <li>usersテーブルにpassword, user_id, roleカラムを追加</li>
                    <li>wp_wqorders_editableテーブルにnotesなど14個のカラムを追加</li>
                    <li>全ユーザーのパスワードを設定</li>
                </ul>
            </div>
            
            <form method="post">
                <button type="submit" name="execute" class="btn" onclick="return confirm('本当に実行しますか？');">
                    🚀 修正を実行する
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