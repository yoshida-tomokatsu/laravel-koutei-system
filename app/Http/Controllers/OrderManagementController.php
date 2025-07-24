<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Models\OrderHandler;
use App\Models\PaymentMethod;
use App\Models\PrintFactory;
use App\Models\SewingFactory;

class OrderManagementController extends Controller
{
    /**
     * Display the management index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orderHandlers = OrderHandler::orderBy('sort_order')->orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('sort_order')->orderBy('name')->get();
        $printFactories = PrintFactory::orderBy('sort_order')->orderBy('name')->get();
        $sewingFactories = SewingFactory::orderBy('sort_order')->orderBy('name')->get();

        return view('management.index', compact(
            'orderHandlers',
            'paymentMethods',
            'printFactories',
            'sewingFactories'
        ));
    }

    // ===== Order Handlers =====

    /**
     * Get all order handlers.
     */
    public function getOrderHandlers(): JsonResponse
    {
        try {
            $handlers = OrderHandler::orderBy('sort_order')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $handlers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '担当者データの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Store a new order handler.
     */
    public function storeOrderHandler(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $handler = OrderHandler::create($validated);

            return response()->json([
                'success' => true,
                'data' => $handler,
                'message' => '担当者を追加しました'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '担当者の追加に失敗しました'
            ], 500);
        }
    }

    /**
     * Update an order handler.
     */
    public function updateOrderHandler(Request $request, $id): JsonResponse
    {
        try {
            $handler = OrderHandler::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $handler->update($validated);

            return response()->json([
                'success' => true,
                'data' => $handler,
                'message' => '担当者を更新しました'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '担当者の更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete an order handler.
     */
    public function deleteOrderHandler($id): JsonResponse
    {
        try {
            $handler = OrderHandler::findOrFail($id);
            $handler->delete();

            return response()->json([
                'success' => true,
                'message' => '担当者を削除しました'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '担当者の削除に失敗しました'
            ], 500);
        }
    }

    // ===== Payment Methods =====

    /**
     * Get all payment methods.
     */
    public function getPaymentMethods(): JsonResponse
    {
        try {
            $methods = PaymentMethod::orderBy('sort_order')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $methods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '支払方法データの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Store a new payment method.
     */
    public function storePaymentMethod(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $method = PaymentMethod::create($validated);

            return response()->json([
                'success' => true,
                'data' => $method,
                'message' => '支払方法を追加しました'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '支払方法の追加に失敗しました'
            ], 500);
        }
    }

    /**
     * Update a payment method.
     */
    public function updatePaymentMethod(Request $request, $id): JsonResponse
    {
        try {
            $method = PaymentMethod::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $method->update($validated);

            return response()->json([
                'success' => true,
                'data' => $method,
                'message' => '支払方法を更新しました'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '支払方法の更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a payment method.
     */
    public function deletePaymentMethod($id): JsonResponse
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            $method->delete();

            return response()->json([
                'success' => true,
                'message' => '支払方法を削除しました'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '支払方法の削除に失敗しました'
            ], 500);
        }
    }

    // ===== Print Factories =====

    /**
     * Get all print factories.
     */
    public function getPrintFactories(): JsonResponse
    {
        try {
            $factories = PrintFactory::orderBy('sort_order')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $factories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'プリント工場データの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Store a new print factory.
     */
    public function storePrintFactory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $factory = PrintFactory::create($validated);

            return response()->json([
                'success' => true,
                'data' => $factory,
                'message' => 'プリント工場を追加しました'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'プリント工場の追加に失敗しました'
            ], 500);
        }
    }

    /**
     * Update a print factory.
     */
    public function updatePrintFactory(Request $request, $id): JsonResponse
    {
        try {
            $factory = PrintFactory::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $factory->update($validated);

            return response()->json([
                'success' => true,
                'data' => $factory,
                'message' => 'プリント工場を更新しました'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'プリント工場の更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a print factory.
     */
    public function deletePrintFactory($id): JsonResponse
    {
        try {
            $factory = PrintFactory::findOrFail($id);
            $factory->delete();

            return response()->json([
                'success' => true,
                'message' => 'プリント工場を削除しました'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'プリント工場の削除に失敗しました'
            ], 500);
        }
    }

    // ===== Sewing Factories =====

    /**
     * Get all sewing factories.
     */
    public function getSewingFactories(): JsonResponse
    {
        try {
            $factories = SewingFactory::orderBy('sort_order')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $factories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '縫製工場データの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Store a new sewing factory.
     */
    public function storeSewingFactory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $factory = SewingFactory::create($validated);

            return response()->json([
                'success' => true,
                'data' => $factory,
                'message' => '縫製工場を追加しました'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '縫製工場の追加に失敗しました'
            ], 500);
        }
    }

    /**
     * Update a sewing factory.
     */
    public function updateSewingFactory(Request $request, $id): JsonResponse
    {
        try {
            $factory = SewingFactory::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            $factory->update($validated);

            return response()->json([
                'success' => true,
                'data' => $factory,
                'message' => '縫製工場を更新しました'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '縫製工場の更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a sewing factory.
     */
    public function deleteSewingFactory($id): JsonResponse
    {
        try {
            $factory = SewingFactory::findOrFail($id);
            $factory->delete();

            return response()->json([
                'success' => true,
                'message' => '縫製工場を削除しました'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '縫製工場の削除に失敗しました'
            ], 500);
        }
    }
} 