<?php

// 実際のデータベースから注文データを取得してorder_idを確認するデバッグスクリプト

require_once __DIR__ . '/vendor/autoload.php';

// Laravel環境のブートストラップ
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

try {
    echo "=== 実際のデータベースから注文データを取得 ===\n";
    
    // 最初の5件の注文データを取得
    $orders = Order::orderBy('created', 'desc')->take(5)->get();
    
    echo "取得した注文件数: " . $orders->count() . "\n\n";
    
    foreach ($orders as $order) {
        echo "--- 注文ID: {$order->id} ---\n";
        echo "フォームID: {$order->formId}\n";
        echo "フォームタイトル: {$order->formTitle}\n";
        echo "顧客: {$order->customer}\n";
        echo "作成日: {$order->created}\n";
        
        // order_idアクセサを確認
        echo "order_id (アクセサ): {$order->order_id}\n";
        
        // ファイル関連メソッドを確認
        echo "hasPdf(): " . ($order->hasPdf() ? 'true' : 'false') . "\n";
        echo "hasImages(): " . ($order->hasImages() ? 'true' : 'false') . "\n";
        
        if ($order->hasPdf()) {
            echo "PDFパス: {$order->getPdfPath()}\n";
        }
        
        // uploads/{order_id}/ フォルダの存在確認
        $uploadPath = public_path("uploads/{$order->order_id}");
        echo "アップロードフォルダ存在: " . (is_dir($uploadPath) ? 'true' : 'false') . "\n";
        
        if (is_dir($uploadPath)) {
            $files = scandir($uploadPath);
            $fileCount = count($files) - 2; // '.'と'..'を除く
            echo "アップロードファイル数: {$fileCount}\n";
            
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "  - {$file}\n";
                }
            }
        }
        
        echo "\n";
    }
    
    echo "=== データベース接続成功 ===\n";
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
    echo "スタックトレース: " . $e->getTraceAsString() . "\n";
} 