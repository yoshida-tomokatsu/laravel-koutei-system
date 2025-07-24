<?php
// Apache用独立データベーステストファイル
// Laravel依存関係を使わない版

// データベース接続設定
$host = 'localhost';
$dbname = 'factory0328_wp2';
$username = 'root';
$password = '';

echo "<!DOCTYPE html>";
echo "<html lang='ja'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>データベーステスト - Apache版</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo ".success { color: #28a745; background-color: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".error { color: #dc3545; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".info { color: #17a2b8; background-color: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo "table { width: 100%; border-collapse: collapse; margin: 20px 0; }";
echo "th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }";
echo "th { background-color: #f8f9fa; font-weight: bold; }";
echo ".section { margin: 30px 0; }";
echo ".section h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>🔍 データベーステスト結果（Apache版）</h1>";

try {
    // PDO接続を試行
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ データベース接続成功！</div>";
    echo "<div class='info'>📊 接続情報: $host -> $dbname</div>";
    
    // テーブル一覧を取得
    echo "<div class='section'>";
    echo "<h2>📋 テーブル一覧</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='info'>テーブル数: " . count($tables) . "</div>";
    echo "<table>";
    echo "<tr><th>テーブル名</th><th>レコード数</th></tr>";
    
    foreach ($tables as $table) {
        try {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $count = $countStmt->fetchColumn();
            echo "<tr><td>$table</td><td>$count</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>$table</td><td>エラー: " . $e->getMessage() . "</td></tr>";
        }
    }
    echo "</table>";
    echo "</div>";
    
    // ユーザー情報を取得
    echo "<div class='section'>";
    echo "<h2>👥 ユーザー情報</h2>";
    try {
        $stmt = $pdo->query("SELECT user_id, display_name, role FROM users LIMIT 10");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='info'>ユーザー数: " . count($users) . "</div>";
        echo "<table>";
        echo "<tr><th>ユーザーID</th><th>表示名</th><th>役割</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['display_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<div class='error'>ユーザー情報取得エラー: " . $e->getMessage() . "</div>";
    }
    echo "</div>";
    
    // 注文情報を取得
    echo "<div class='section'>";
    echo "<h2>📦 注文情報</h2>";
    try {
        $stmt = $pdo->query("SELECT order_id, JSON_UNQUOTE(JSON_EXTRACT(order_data, '$.customer_name')) as customer_name, JSON_UNQUOTE(JSON_EXTRACT(order_data, '$.company_name')) as company_name FROM wp_wqorders_editable ORDER BY order_id DESC LIMIT 10");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='info'>注文数: " . count($orders) . "</div>";
        echo "<table>";
        echo "<tr><th>注文ID</th><th>顧客名</th><th>会社名</th></tr>";
        
        foreach ($orders as $order) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
            echo "<td>" . htmlspecialchars($order['customer_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($order['company_name'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<div class='error'>注文情報取得エラー: " . $e->getMessage() . "</div>";
    }
    echo "</div>";
    
    // システム情報
    echo "<div class='section'>";
    echo "<h2>🛠️ システム情報</h2>";
    echo "<table>";
    echo "<tr><th>項目</th><th>値</th></tr>";
    echo "<tr><td>PHP版本</td><td>" . phpversion() . "</td></tr>";
    echo "<tr><td>MySQL版本</td><td>" . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "</td></tr>";
    echo "<tr><td>接続方式</td><td>Apache + PHP</td></tr>";
    echo "<tr><td>実行時間</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ データベース接続エラー: " . $e->getMessage() . "</div>";
    echo "<div class='info'>接続パラメータ: host=$host, dbname=$dbname, username=$username</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?> 