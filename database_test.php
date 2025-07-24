<?php
/**
 * Laravel データベース接続テスト
 */

// Laravel環境の初期化
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// カーネルを起動
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// 環境を設定
$app->detectEnvironment(function () {
    return 'local';
});

// 設定を読み込み
$app->make('config');

echo "=== Laravel データベース接続テスト ===\n\n";

try {
    // データベース接続テスト
    $pdo = DB::connection()->getPdo();
    echo "✅ データベース接続成功\n";
    
    // データベース情報を表示
    $dbname = DB::connection()->getDatabaseName();
    echo "📊 データベース名: {$dbname}\n";
    
    // テーブルの確認
    $tables = DB::select('SHOW TABLES');
    echo "📋 テーブル数: " . count($tables) . "\n";
    
    // usersテーブルの確認
    $userCount = DB::table('users')->count();
    echo "👤 ユーザー数: {$userCount}\n";
    
    // wp_wqorders_editableテーブルの確認
    $orderCount = DB::table('wp_wqorders_editable')->count();
    echo "📦 注文数: {$orderCount}\n";
    
    echo "\n=== サンプルデータ確認 ===\n";
    
    // サンプルユーザーを取得
    $users = DB::table('users')->limit(3)->get();
    echo "サンプルユーザー:\n";
    foreach ($users as $user) {
        echo "- {$user->name} ({$user->user_id}) - {$user->role}\n";
    }
    
    // サンプル注文を取得
    echo "\nサンプル注文:\n";
    $orders = DB::table('wp_wqorders_editable')
                ->select('order_id', 'customer_name', 'company_name', 'order_date')
                ->orderBy('order_date', 'desc')
                ->limit(3)
                ->get();
                
    foreach ($orders as $order) {
        echo "- {$order->order_id}: {$order->customer_name} ({$order->company_name}) - {$order->order_date}\n";
    }
    
    echo "\n✅ データベーステスト完了\n";
    
} catch (Exception $e) {
    echo "❌ エラー: " . $e->getMessage() . "\n";
    echo "詳細: " . $e->getTraceAsString() . "\n";
} 