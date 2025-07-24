<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\ApiAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 認証不要のルート
Route::post('/login', [ApiAuthController::class, 'login']);

// 認証が必要なルート
Route::middleware('auth:sanctum')->group(function () {
    // 認証関連
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::post('/refresh', [ApiAuthController::class, 'refresh']);
    
    // 注文関連
    Route::apiResource('orders', OrderController::class);
    
    // 既存システムとの互換性のための追加ルート
    Route::get('/orders/search/{query}', [OrderController::class, 'search']);
    Route::get('/orders/category/{category}', [OrderController::class, 'byCategory']);
    Route::get('/orders/status/{status}', [OrderController::class, 'byStatus']);
    Route::patch('/orders/{order}/update-status', [OrderController::class, 'updateStatus']);
    
    // 注文情報更新ルート
    Route::patch('/orders/{orderId}/update-info', [OrderController::class, 'updateOrderInfo']);
});

// 既存システムとの互換性のための非認証ルート（必要に応じて）
Route::group(['prefix' => 'legacy'], function () {
    Route::get('/orders', [OrderController::class, 'legacyIndex']);
    Route::get('/orders/{order}', [OrderController::class, 'legacyShow']);
    Route::post('/orders/{order}/update', [OrderController::class, 'legacyUpdate']);
});

// ヘルスチェック用ルート
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// システム情報取得ルート
Route::get('/system-info', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'database_connected' => true,
        'timestamp' => now()
    ]);
});
