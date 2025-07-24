<?php
// データベース接続テストスクリプト
header('Content-Type: text/plain; charset=UTF-8');

echo "データベース接続テスト開始\n";
echo "時刻: " . date('Y-m-d H:i:s') . "\n";
echo "----------------------\n";

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=factory0328_wp2;charset=utf8',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
    
    echo "✅ データベース接続成功\n";
    
    // テーブル存在確認
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'wp_wqorders_editable'");
    $stmt->execute();
    $table_exists = $stmt->fetchColumn();
    
    if ($table_exists) {
        echo "✅ テーブル wp_wqorders_editable 存在確認\n";
        
        // データ件数確認
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wp_wqorders_editable");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "✅ データ件数: {$count}件\n";
        
        // 除外対象件数確認
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wp_wqorders_editable WHERE formTitle IN ('お問い合わせ', 'サンプル請求')");
        $stmt->execute();
        $excluded_count = $stmt->fetchColumn();
        echo "✅ 除外対象件数: {$excluded_count}件\n";
        
        // 表示対象件数確認
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wp_wqorders_editable WHERE formTitle NOT IN ('お問い合わせ', 'サンプル請求')");
        $stmt->execute();
        $display_count = $stmt->fetchColumn();
        echo "✅ 表示対象件数: {$display_count}件\n";
        
    } else {
        echo "❌ テーブル wp_wqorders_editable が見つかりません\n";
    }
    
} catch (PDOException $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "\n";
}

echo "----------------------\n";
echo "テスト完了\n";
?> 