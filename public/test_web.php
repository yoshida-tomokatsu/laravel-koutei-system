<?php
/**
 * Laravel Web テスト
 * 直接ブラウザでアクセス可能なテストページ
 */

// Laravel環境の読み込み
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    // データベース設定を読み込み
    $env = [];
    if (file_exists(__DIR__ . '/.env')) {
        $content = file_get_contents(__DIR__ . '/.env');
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
    }
    
    $dbHost = $env['DB_HOST'] ?? 'localhost';
    $dbName = $env['DB_DATABASE'] ?? 'factory0328_wp2';
    $dbUser = $env['DB_USERNAME'] ?? 'root';
    $dbPass = $env['DB_PASSWORD'] ?? '';
    
} catch (Exception $e) {
    // Laravel環境なしでも動作するようにフォールバック
    $dbHost = 'localhost';
    $dbName = 'factory0328_wp2';
    $dbUser = 'root';
    $dbPass = '';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工程管理システム - Laravel テスト</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 18px;
            color: #7f8c8d;
        }
        .status {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .success { background: #d5f4e6; color: #27ae60; }
        .error { background: #fdeaea; color: #e74c3c; }
        .info { background: #e3f2fd; color: #2196f3; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .data-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">🚀 工程管理システム</h1>
            <p class="subtitle">Laravel 移行テスト</p>
        </div>
        
        <?php
        try {
            // データベース接続テスト
            $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            echo '<div class="status success">';
            echo '<h3>✅ データベース接続成功</h3>';
            echo "<p>データベース: {$dbName}@{$dbHost}</p>";
            echo '</div>';
            
            // ユーザー情報を取得
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
            $userCount = $stmt->fetch()['count'];
            
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM wp_wqorders_editable');
            $orderCount = $stmt->fetch()['count'];
            
            echo '<div class="status info">';
            echo '<h3>📊 データ統計</h3>';
            echo "<p>ユーザー数: {$userCount} 人</p>";
            echo "<p>注文数: {$orderCount} 件</p>";
            echo '</div>';
            
            // サンプルユーザーを表示
            echo '<h3>👤 サンプルユーザー</h3>';
            $stmt = $pdo->query('SELECT user_id, name, role FROM users LIMIT 5');
            $users = $stmt->fetchAll();
            
            echo '<table class="data-table">';
            echo '<tr><th>ユーザーID</th><th>名前</th><th>役割</th></tr>';
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['user_id']}</td>";
                echo "<td>{$user['name']}</td>";
                echo "<td>" . ($user['role'] === 'admin' ? '管理者' : '従業員') . "</td>";
                echo "</tr>";
            }
            echo '</table>';
            
            // サンプル注文を表示
            echo '<h3>📦 サンプル注文</h3>';
            $stmt = $pdo->query('SELECT order_id, customer_name, company_name, order_date FROM wp_wqorders_editable ORDER BY order_date DESC LIMIT 5');
            $orders = $stmt->fetchAll();
            
            echo '<table class="data-table">';
            echo '<tr><th>注文ID</th><th>顧客名</th><th>会社名</th><th>注文日</th></tr>';
            foreach ($orders as $order) {
                echo "<tr>";
                echo "<td>{$order['order_id']}</td>";
                echo "<td>{$order['customer_name']}</td>";
                echo "<td>{$order['company_name']}</td>";
                echo "<td>{$order['order_date']}</td>";
                echo "</tr>";
            }
            echo '</table>';
            
        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '<h3>❌ エラー発生</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="status info">
            <h3>🔗 テストリンク</h3>
            <a href="../index.php" class="btn">📊 現在のシステム</a>
            <a href="../login.html" class="btn">🔐 ログインページ</a>
            <a href="test_web.php" class="btn">🔄 このページを再読み込み</a>
        </div>
        
        <div class="status">
            <h3>📋 システム情報</h3>
            <p><strong>PHP バージョン:</strong> <?= PHP_VERSION ?></p>
            <p><strong>現在時刻:</strong> <?= date('Y-m-d H:i:s') ?></p>
            <p><strong>ファイルパス:</strong> <?= __FILE__ ?></p>
        </div>
    </div>
</body>
</html> 