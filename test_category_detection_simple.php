<?php
// シンプルなジャンル判別ロジックテストスクリプト
header('Content-Type: text/plain; charset=UTF-8');

echo "ジャンル判別ロジックテスト開始\n";
echo "時刻: " . date('Y-m-d H:i:s') . "\n";
echo "========================\n";

// ジャンル判別関数
function getProductCategory($formTitle) {
    // 優先順位に基づいてジャンル判別
    if (strpos($formTitle, 'リボン') !== false) {
        return 'リボン スカーフ';
    }
    if (strpos($formTitle, 'シルク') !== false) {
        return 'シルク スカーフ';
    }
    if (strpos($formTitle, 'ポケットチーフ') !== false || strpos($formTitle, 'チーフ') !== false) {
        return 'ポケットチーフ';
    }
    if (strpos($formTitle, 'スカーフタイ') !== false || strpos($formTitle, 'タイ') !== false) {
        return 'スカーフタイ';
    }
    if (strpos($formTitle, 'ストール') !== false) {
        return 'ストール';
    }
    if (strpos($formTitle, 'ポリエステル') !== false || strpos($formTitle, 'スカーフ') !== false) {
        return 'ポリエステル スカーフ';
    }
    
    // デフォルト
    return 'ポリエステル スカーフ';
}

// カテゴリ色取得関数
function getProductCategoryColor($category) {
    $colors = [
        'ポリエステル スカーフ' => '#3498db',  // 青
        'シルク スカーフ' => '#2ecc71',       // 緑
        'リボン スカーフ' => '#e67e22',       // オレンジ
        'スカーフタイ' => '#663399',          // 紫
        'ストール' => '#e74c3c',            // 赤
        'ポケットチーフ' => '#95a5a6'         // グレー
    ];
    
    return $colors[$category] ?? '#3498db';
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=factory0328_wp2;charset=utf8',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // 表示対象データの最新20件を取得
    $stmt = $pdo->prepare("
        SELECT id, formTitle 
        FROM wp_wqorders_editable 
        WHERE formTitle NOT IN ('お問い合わせ', 'サンプル請求')
        ORDER BY id DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "最新20件のジャンル判別結果:\n";
    echo "----------------------------\n";
    
    foreach ($orders as $order) {
        $category = getProductCategory($order['formTitle']);
        $color = getProductCategoryColor($category);
        
        echo sprintf("ID: %4d | %-30s -> %-18s (%s)\n", 
            $order['id'], 
            $order['formTitle'], 
            $category, 
            $color
        );
    }
    
    // 全データのカテゴリ別集計
    $stmt = $pdo->prepare("
        SELECT formTitle 
        FROM wp_wqorders_editable 
        WHERE formTitle NOT IN ('お問い合わせ', 'サンプル請求')
    ");
    $stmt->execute();
    $allOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $categoryCounts = [];
    foreach ($allOrders as $order) {
        $category = getProductCategory($order['formTitle']);
        $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
    }
    
    echo "\nカテゴリ別集計:\n";
    echo "----------------------------\n";
    foreach ($categoryCounts as $category => $count) {
        echo sprintf("%-18s : %3d件\n", $category, $count);
    }
    
    echo "\n利用可能なカテゴリ一覧:\n";
    echo "----------------------------\n";
    $categories = [
        'ポリエステル スカーフ',
        'シルク スカーフ',
        'リボン スカーフ',
        'スカーフタイ',
        'ストール',
        'ポケットチーフ'
    ];
    
    foreach ($categories as $category) {
        $color = getProductCategoryColor($category);
        echo sprintf("%-18s : %s\n", $category, $color);
    }
    
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage() . "\n";
}

echo "========================\n";
echo "テスト完了\n";
?> 