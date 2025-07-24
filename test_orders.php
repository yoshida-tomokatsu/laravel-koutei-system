<?php
// Orders画面アクセステストスクリプト
header('Content-Type: text/plain; charset=UTF-8');

echo "Orders画面アクセステスト開始\n";
echo "時刻: " . date('Y-m-d H:i:s') . "\n";
echo "----------------------\n";

// cURLを使用してHTTP応答を確認
function checkUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'error' => $error,
        'response' => $response
    ];
}

// ルートパス確認
echo "1. ルートパス確認 (http://127.0.0.1:8000/)\n";
$result = checkUrl('http://127.0.0.1:8000/');
echo "   HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "   Error: " . $result['error'] . "\n";
}
echo "\n";

// Orders画面確認
echo "2. Orders画面確認 (http://127.0.0.1:8000/orders)\n";
$result = checkUrl('http://127.0.0.1:8000/orders');
echo "   HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "   Error: " . $result['error'] . "\n";
}
echo "\n";

// 直接的なデータベース操作テスト
echo "3. 直接的なデータベース操作テスト\n";
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=factory0328_wp2;charset=utf8',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wp_wqorders_editable WHERE formTitle NOT IN ('お問い合わせ', 'サンプル請求')");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    echo "   表示対象データ件数: {$count}件\n";
    
    // 最初の3件を取得してテスト
    $stmt = $pdo->prepare("SELECT id, formTitle, content FROM wp_wqorders_editable WHERE formTitle NOT IN ('お問い合わせ', 'サンプル請求') ORDER BY id DESC LIMIT 3");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   最新3件のデータ:\n";
    foreach ($orders as $order) {
        echo "     ID: {$order['id']}, フォーム: {$order['formTitle']}\n";
        
        // contentフィールドのJSON解析テスト
        $content = json_decode($order['content'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "     JSON解析: 成功\n";
            if (isset($content['attrs']) && is_array($content['attrs'])) {
                echo "     attrs配列: 存在 (" . count($content['attrs']) . "個)\n";
            } else {
                echo "     attrs配列: 存在しません\n";
            }
        } else {
            echo "     JSON解析: エラー - " . json_last_error_msg() . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "   データベースエラー: " . $e->getMessage() . "\n";
}

echo "----------------------\n";
echo "テスト完了\n";
?> 