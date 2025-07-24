<?php

// 実際にファイルが存在する可能性のある低いIDの注文データを確認

require_once __DIR__ . '/vendor/autoload.php';

// Laravel環境のブートストラップ
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

try {
    echo "=== 低いIDの注文データを確認（ファイルが存在する可能性） ===\n";
    
    // ID 1-100の注文データを取得
    $orders = Order::where('id', '<=', 100)->orderBy('id', 'asc')->take(10)->get();
    
    echo "取得した注文件数: " . $orders->count() . "\n\n";
    
    if ($orders->count() == 0) {
        echo "低いIDの注文データが見つかりません。\n";
        
        // 代わりに、PDFファイルに対応するIDを作成
        echo "テスト用のファイルアクセスを確認します。\n";
        
        // 実際にあるPDFファイルに対応するorder_idを生成
        $testIds = ['483', '484', '485', '486', '487'];
        
        foreach ($testIds as $testId) {
            $orderIdPadded = str_pad($testId, 4, '0', STR_PAD_LEFT);
            $orderId = "#{$orderIdPadded}";
            
            // PDFファイルのパスを確認
            $pdfPath1 = "/aforms-pdf/01-000/{$orderIdPadded}.pdf";
            $pdfPath2 = "/aforms-pdf/01-001/{$orderIdPadded}.pdf";
            
            $pdfExists1 = file_exists(public_path($pdfPath1));
            $pdfExists2 = file_exists(public_path($pdfPath2));
            
            echo "--- テストID: {$orderId} ---\n";
            echo "PDFパス1: {$pdfPath1} - 存在: " . ($pdfExists1 ? 'true' : 'false') . "\n";
            echo "PDFパス2: {$pdfPath2} - 存在: " . ($pdfExists2 ? 'true' : 'false') . "\n";
            
            // uploadsフォルダ確認
            $uploadPath = public_path("uploads/{$orderId}");
            echo "アップロードフォルダ: {$uploadPath} - 存在: " . (is_dir($uploadPath) ? 'true' : 'false') . "\n";
            
            if (is_dir($uploadPath)) {
                $files = scandir($uploadPath);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        echo "  - {$file}\n";
                    }
                }
            }
            
            echo "\n";
        }
    } else {
        foreach ($orders as $order) {
            echo "--- 注文ID: {$order->id} ---\n";
            echo "フォームID: {$order->formId}\n";
            echo "フォームタイトル: {$order->formTitle}\n";
            echo "order_id (アクセサ): {$order->order_id}\n";
            echo "hasPdf(): " . ($order->hasPdf() ? 'true' : 'false') . "\n";
            echo "hasImages(): " . ($order->hasImages() ? 'true' : 'false') . "\n";
            
            if ($order->hasPdf()) {
                echo "PDFパス: {$order->getPdfPath()}\n";
            }
            
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
    echo "スタックトレース: " . $e->getTraceAsString() . "\n";
} 