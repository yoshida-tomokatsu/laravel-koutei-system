<?php

// 実際のデータベースから取得したデータでファイル検出をテスト

require_once __DIR__ . '/vendor/autoload.php';

// Laravel環境のブートストラップ
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

try {
    echo "=== 実際のデータベースファイル検出テスト ===\n";
    
    // 最新の5件の注文データを取得
    $orders = Order::orderBy('created', 'desc')->take(5)->get();
    
    echo "取得した注文件数: " . $orders->count() . "\n\n";
    
    foreach ($orders as $order) {
        echo "--- 注文ID: {$order->id} ---\n";
        echo "order_id (アクセサ): {$order->order_id}\n";
        
        // hasPdf()の詳細チェック
        echo "=== PDF検出テスト ===\n";
        $pdfPath = $order->getPdfPath();
        echo "PDFパス: " . ($pdfPath ? $pdfPath : 'null') . "\n";
        echo "hasPdf(): " . ($order->hasPdf() ? 'true' : 'false') . "\n";
        
        if ($pdfPath) {
            $fullPath = public_path($pdfPath);
            echo "フルパス: {$fullPath}\n";
            echo "ファイル存在: " . (file_exists($fullPath) ? 'true' : 'false') . "\n";
        }
        
        // hasImages()の詳細チェック
        echo "=== 画像検出テスト ===\n";
        $uploadPath = public_path("uploads/{$order->order_id}");
        echo "アップロードパス: {$uploadPath}\n";
        echo "フォルダ存在: " . (is_dir($uploadPath) ? 'true' : 'false') . "\n";
        echo "hasImages(): " . ($order->hasImages() ? 'true' : 'false') . "\n";
        
        if (is_dir($uploadPath)) {
            $files = scandir($uploadPath);
            $imageFiles = array_filter($files, function($file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            });
            echo "画像ファイル数: " . count($imageFiles) . "\n";
            foreach ($imageFiles as $imageFile) {
                echo "  - {$imageFile}\n";
            }
        }
        
        echo "\n";
    }
    
    // 実際にファイルが存在する可能性のあるIDをチェック
    echo "=== 低ID注文のファイル存在チェック ===\n";
    $lowIdOrders = Order::where('id', '<=', 100)->orderBy('id', 'asc')->take(3)->get();
    
    foreach ($lowIdOrders as $order) {
        echo "注文ID {$order->id} ({$order->order_id}): ";
        echo "PDF=" . ($order->hasPdf() ? 'あり' : 'なし') . ", ";
        echo "画像=" . ($order->hasImages() ? 'あり' : 'なし') . "\n";
    }
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
    echo "スタックトレース: " . $e->getTraceAsString() . "\n";
} 