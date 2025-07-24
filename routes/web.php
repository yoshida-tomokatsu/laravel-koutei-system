<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\PdfController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 保護されたルート（認証が必要）
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/images/{orderId}', [OrderController::class, 'viewImages'])->name('images.show');
});

// 一時的に認証なしでアクセス可能
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::patch('/orders/{orderId}/update-info', [OrderController::class, 'updateOrderInfo'])->name('orders.update-info');

// PDF管理ルート
Route::prefix('pdf')->group(function () {
    Route::get('/', [PdfController::class, 'index'])->name('pdf.index');
    Route::get('/search', [PdfController::class, 'search'])->name('pdf.search');
    Route::get('/{orderId}', [PdfController::class, 'show'])->name('pdf.show');
    Route::get('/{orderId}/viewer', [PdfController::class, 'viewer'])->name('pdf.viewer');
    Route::get('/{orderId}/manage', [PdfController::class, 'manage'])->name('pdf.manage');
    Route::get('/{orderId}/list', [PdfController::class, 'list'])->name('pdf.list');
    Route::post('/{orderId}/upload', [PdfController::class, 'upload'])->name('pdf.upload');
    Route::delete('/{orderId}/delete', [PdfController::class, 'delete'])->name('pdf.delete');
    Route::post('/{orderId}/reorder', [PdfController::class, 'reorderPages'])->name('pdf.reorder');
    Route::post('/{orderId}/rename', [PdfController::class, 'rename'])->name('pdf.rename');
    Route::get('/{orderId}/info', [PdfController::class, 'getOrderPdf'])->name('pdf.info');
});

// テスト用ルート（認証なし）
Route::get('/test-files', function () {
    // テスト用のダミーデータを作成
    $testOrders = [
        (object) [
            'id' => 1,
            'customer_name' => 'テスト顧客1',
            'company_name' => 'テスト会社1',
            'order_date' => '2025-01-01',
            'notes' => 'テスト注文',
            'category' => 'high',
            'status' => 'pending'
        ],
        (object) [
            'id' => 2,
            'customer_name' => 'テスト顧客2',
            'company_name' => 'テスト会社2',
            'order_date' => '2025-01-02',
            'notes' => 'テスト注文2',
            'category' => 'medium',
            'status' => 'in_progress'
        ],
        (object) [
            'id' => 3,
            'customer_name' => 'テスト顧客3',
            'company_name' => 'テスト会社3',
            'order_date' => '2025-01-03',
            'notes' => 'テスト注文3',
            'category' => 'low',
            'status' => 'completed'
        ]
    ];
    
    return view('test-files', compact('testOrders'));
})->name('test-files');

// 画像表示用ルート（認証なし）
Route::get('/test-images/{orderId}', function ($orderId) {
    $imagePath = public_path("uploads/{$orderId}");
    $images = [];
    
    if (is_dir($imagePath)) {
        $files = scandir($imagePath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                $images[] = $file;
            }
        }
    }
    
    return view('test-images', compact('orderId', 'images'));
})->name('test-images');

// Order Management routes
Route::prefix('management')->group(function () {
    Route::get('/', [OrderManagementController::class, 'index'])->name('management.index');
    
    // Order Handlers
    Route::get('/api/order-handlers', [OrderManagementController::class, 'getOrderHandlers']);
    Route::post('/api/order-handlers', [OrderManagementController::class, 'storeOrderHandler']);
    Route::put('/api/order-handlers/{id}', [OrderManagementController::class, 'updateOrderHandler']);
    Route::delete('/api/order-handlers/{id}', [OrderManagementController::class, 'deleteOrderHandler']);
    
    // Payment Methods
    Route::get('/api/payment-methods', [OrderManagementController::class, 'getPaymentMethods']);
    Route::post('/api/payment-methods', [OrderManagementController::class, 'storePaymentMethod']);
    Route::put('/api/payment-methods/{id}', [OrderManagementController::class, 'updatePaymentMethod']);
    Route::delete('/api/payment-methods/{id}', [OrderManagementController::class, 'deletePaymentMethod']);
    
    // Print Factories
    Route::get('/api/print-factories', [OrderManagementController::class, 'getPrintFactories']);
    Route::post('/api/print-factories', [OrderManagementController::class, 'storePrintFactory']);
    Route::put('/api/print-factories/{id}', [OrderManagementController::class, 'updatePrintFactory']);
    Route::delete('/api/print-factories/{id}', [OrderManagementController::class, 'deletePrintFactory']);
    
    // Sewing Factories
    Route::get('/api/sewing-factories', [OrderManagementController::class, 'getSewingFactories']);
    Route::post('/api/sewing-factories', [OrderManagementController::class, 'storeSewingFactory']);
    Route::put('/api/sewing-factories/{id}', [OrderManagementController::class, 'updateSewingFactory']);
    Route::delete('/api/sewing-factories/{id}', [OrderManagementController::class, 'deleteSewingFactory']);
});

// Laravel Breeze認証ルート
require __DIR__.'/auth.php';
