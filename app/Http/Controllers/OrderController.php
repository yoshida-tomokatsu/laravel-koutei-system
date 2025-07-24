<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display the orders page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $databaseError = null;
        
        try {
            // Try to get real data from database
            $perPage = $request->get('per_page', 10);
            $orders = Order::excludeInquiryAndSample()->orderBy('created', 'desc')->paginate($perPage);
            
            $totalOrders = Order::excludeInquiryAndSample()->count();
            $quoteOrders = Order::excludeInquiryAndSample()->where('formTitle', 'LIKE', '%見積%')->count();
            $inquiryOrders = Order::excludeInquiryAndSample()->where('formTitle', 'LIKE', '%お問い合わせ%')->count();
            
            // データベース接続が成功した場合
            // 表示モード設定
            $viewMode = $request->get('view', 'detailed');
            $currentTab = $request->get('tab', 'all');
            $currentPage = $request->get('page', 1);
            
            return view('orders.index', compact(
                'orders',
                'totalOrders',
                'quoteOrders',
                'inquiryOrders',
                'viewMode',
                'currentTab',
                'currentPage',
                'databaseError'
            ));
            
        } catch (\Exception $e) {
            // Store error information for display
            $databaseError = [
                'message' => $e->getMessage(),
                'type' => 'database_connection_failed',
                'details' => [
                    'host' => config('database.connections.mysql.host'),
                    'database' => config('database.connections.mysql.database'),
                    'username' => config('database.connections.mysql.username'),
                ]
            ];
            
            // Use dummy data as fallback
            $dummyData = [
                [
                    'id' => 1,
                    'formId' => 1,
                    'formTitle' => 'お見積り依頼',
                    'customer' => 'テスト顧客1',
                    'total' => 50000.00,
                    'created' => '2025-07-13 10:00:00',
                    'last_updated' => null,
                    'content' => json_encode([
                        'attrs' => [
                            ['type' => 'Name', 'name' => 'お名前', 'value' => 'テスト顧客1'],
                            ['type' => 'Company', 'name' => '会社名', 'value' => 'テスト会社1'],
                            ['type' => 'Email', 'name' => 'メールアドレス', 'value' => 'test1@example.com'],
                            ['type' => 'Phone', 'name' => '電話番号', 'value' => '090-1234-5678'],
                            ['type' => 'Date', 'name' => '納期希望日', 'value' => '2025-07-20'],
                            ['type' => 'Select', 'name' => '掲載許可', 'value' => 'する'],
                        ]
                    ])
                ],
                [
                    'id' => 2,
                    'formId' => 2,
                    'formTitle' => 'お見積り依頼',
                    'customer' => 'テスト顧客2',
                    'total' => 75000.00,
                    'created' => '2025-07-12 15:30:00',
                    'last_updated' => null,
                    'content' => json_encode([
                        'attrs' => [
                            ['type' => 'Name', 'name' => 'お名前', 'value' => 'テスト顧客2'],
                            ['type' => 'Company', 'name' => '会社名', 'value' => 'テスト会社2'],
                            ['type' => 'Email', 'name' => 'メールアドレス', 'value' => 'test2@example.com'],
                            ['type' => 'Phone', 'name' => '電話番号', 'value' => '090-9876-5432'],
                            ['type' => 'Date', 'name' => '納期希望日', 'value' => '2025-07-25'],
                            ['type' => 'Select', 'name' => '掲載許可', 'value' => 'する'],
                        ]
                    ])
                ],
                [
                    'id' => 3,
                    'formId' => 3,
                    'formTitle' => 'お問い合わせ',
                    'customer' => 'テスト顧客3',
                    'total' => 25000.00,
                    'created' => '2025-07-11 09:15:00',
                    'last_updated' => '2025-07-11 18:00:00',
                    'content' => json_encode([
                        'attrs' => [
                            ['type' => 'Name', 'name' => 'お名前', 'value' => 'テスト顧客3'],
                            ['type' => 'Company', 'name' => '会社名', 'value' => 'テスト会社3'],
                            ['type' => 'Email', 'name' => 'メールアドレス', 'value' => 'test3@example.com'],
                            ['type' => 'Phone', 'name' => '電話番号', 'value' => '090-1111-2222'],
                            ['type' => 'Date', 'name' => '納期希望日', 'value' => '2025-07-13'],
                            ['type' => 'Select', 'name' => '掲載許可', 'value' => 'しない'],
                        ]
                    ])
                ]
            ];
            
            // Create Order model instances from dummy data
            $orderModels = collect($dummyData)->map(function ($data) {
                $order = new Order();
                $order->fill($data);
                $order->setAttribute('id', $data['id']);
                // 実際にファイルが存在するorder_idを設定
                $order->setAttribute('order_id', '#' . str_pad($data['id'], 4, '0', STR_PAD_LEFT));
                return $order;
            });
            
            // Create mock pagination
            $perPage = $request->get('per_page', 10);
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                $orderModels,
                $orderModels->count(),
                $perPage,
                $request->get('page', 1),
                ['path' => $request->url()]
            );
            
            $totalOrders = 3;
            $quoteOrders = 2;
            $inquiryOrders = 1;
        
            // 表示モード設定
            $viewMode = $request->get('view', 'detailed');
            $currentTab = $request->get('tab', 'all');
            $currentPage = $request->get('page', 1);
            
            return view('orders.index', compact(
                'orders',
                'totalOrders',
                'quoteOrders',
                'inquiryOrders',
                'viewMode',
                'currentTab',
                'currentPage',
                'databaseError'
            ));
        }
    }

    /**
     * Get orders data for API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $query = Order::excludeInquiryAndSample();
            
            // 検索機能
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('formTitle', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }
            
            // フィルタリング機能
            if ($request->has('form_title')) {
                $query->byFormTitle($request->get('form_title'));
            }
            
            if ($request->has('status')) {
                $status = $request->get('status');
                switch ($status) {
                    case 'quote':
                        $query->where('formTitle', 'like', '%見積%');
                        break;
                    case 'inquiry':
                        $query->where('formTitle', 'like', '%お問い合わせ%');
                        break;
                    case 'recent':
                        $query->where('created', '>=', Carbon::now()->subDays(7)->timestamp);
                        break;
                    case 'high_value':
                        $query->where('total', '>=', 50000);
                        break;
                }
            }
            
            // 日付範囲フィルタ
            if ($request->has('date_from') && $request->has('date_to')) {
                $dateFrom = Carbon::parse($request->get('date_from'))->timestamp;
                $dateTo = Carbon::parse($request->get('date_to'))->timestamp;
                $query->byDateRange($dateFrom, $dateTo);
            }
            
            // ソート機能
            $sortBy = $request->get('sort_by', 'created');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // ページネーション
            $perPage = $request->get('per_page', 20);
            $orders = $query->paginate($perPage);
            
            // 追加情報の付与
            $orders->getCollection()->transform(function ($order) {
                $order->order_id = $order->order_id;
                $order->category_color = $order->getCategoryColor();
                $order->status = $order->getStatus();
                $order->status_label = $order->getStatusLabel();
                $order->formatted_created = $order->formatted_created;
                $order->formatted_created_date = $order->formatted_created_date;
                $order->customer_name = $order->customer_name;
                $order->customer_email = $order->customer_email;
                $order->customer_phone = $order->customer_phone;
                $order->customer_address = $order->customer_address;
                $order->company_name = $order->company_name;
                $order->delivery_hope_date = $order->delivery_hope_date;
                $order->publication_permission = $order->publication_permission;
                $order->days_since_creation = $order->getDaysSinceCreation();
                $order->is_recent = $order->isRecent();
                $order->has_pdf = $order->hasPdf();
                $order->pdf_path = $order->getPdfPath();
                return $order;
            });

            // 統計情報の追加
            $stats = [
                'total_orders' => Order::excludeInquiryAndSample()->count(),
                'quote_orders' => Order::excludeInquiryAndSample()->where('formTitle', 'like', '%見積%')->count(),
                'inquiry_orders' => Order::excludeInquiryAndSample()->where('formTitle', 'like', '%お問い合わせ%')->count(),
                'recent_orders' => Order::excludeInquiryAndSample()->where('created', '>=', Carbon::now()->subDays(7)->timestamp)->count(),
                'high_value_orders' => Order::excludeInquiryAndSample()->where('total', '>=', 50000)->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $orders,
                'stats' => $stats,
                'message' => '注文データを取得しました'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '注文データの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created order.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'formId' => 'required|integer',
                'formTitle' => 'required|string|max:100',
                'customer' => 'nullable|integer',
                'total' => 'required|numeric|min:0',
                'content' => 'required|array',
            ]);

            $validated['created'] = time();
            
            $order = Order::create($validated);
            
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => '注文を作成しました'
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
                'message' => '注文の作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            
            // 追加情報の付与
            $order->order_id = $order->order_id;
            $order->category_color = $order->getCategoryColor();
            $order->status = $order->getStatus();
            $order->status_label = $order->getStatusLabel();
            $order->formatted_created = $order->formatted_created;
            $order->customer_name = $order->customer_name;
            $order->customer_email = $order->customer_email;
            $order->customer_phone = $order->customer_phone;
            $order->customer_address = $order->customer_address;
            $order->company_name = $order->company_name;
            $order->delivery_hope_date = $order->delivery_hope_date;
            $order->publication_permission = $order->publication_permission;
            $order->has_pdf = $order->hasPdf();
            $order->pdf_path = $order->getPdfPath();
            
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => '注文詳細を取得しました'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '注文の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified order.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            
            $validated = $request->validate([
                'formId' => 'sometimes|required|integer',
                'formTitle' => 'sometimes|required|string|max:100',
                'customer' => 'sometimes|nullable|integer',
                'total' => 'sometimes|required|numeric|min:0',
                'content' => 'sometimes|required|array',
            ]);
            
            $order->update($validated);
            
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => '注文を更新しました'
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
                'message' => '注文の更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            
            return response()->json([
                'success' => true,
                'message' => '注文を削除しました'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '注文の削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a specific field of an order.
     *
     * @param Request $request
     * @param int $orderId
     * @return JsonResponse
     */
    public function updateField(Request $request, $orderId): JsonResponse
    {
        try {
            $order = Order::where('order_id', $orderId)->firstOrFail();
            
            $validated = $request->validate([
                'field' => 'required|string',
                'value' => 'nullable|string'
            ]);
            
            $field = $validated['field'];
            $value = $validated['value'];
            
            // 日付フィールドの処理
            if (in_array($field, ['print_request_date', 'print_deadline', 'sewing_request_date', 'sewing_deadline', 'shipping_date', 'delivery_date'])) {
                if ($value) {
                    $value = \Carbon\Carbon::parse($value);
                }
            }
            
            // 許可されたフィールドのみ更新
            $allowedFields = [
                'order_handler_id',
                'image_sent_date',
                'payment_method_id',
                'payment_completed_date',
                'print_request_date',
                'print_factory_id',
                'print_deadline',
                'sewing_request_date',
                'sewing_factory_id',
                'sewing_deadline',
                'quality_check_date',
                'shipping_date',
                'notes',
                'customer_name',
                'company_name',
                'publication_permission'
            ];
            
            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => '更新できないフィールドです'
                ], 400);
            }
            
            $order->update([$field => $value]);
            
            return response()->json([
                'success' => true,
                'message' => 'フィールドを更新しました',
                'data' => [
                    'field' => $field,
                    'value' => $value,
                    'order_id' => $orderId
                ]
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
                'message' => 'フィールドの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order information (batch update).
     *
     * @param Request $request
     * @param int $orderId
     * @return JsonResponse
     */
    public function updateOrderInfo(Request $request, $orderId): JsonResponse
    {
        try {
            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => '注文が見つかりません'
                ], 404);
            }
            
            // プロセスフィールドも含む包括的なバリデーション
            $validated = $request->validate([
                // 基本注文情報
                'category' => 'nullable|string|max:255',
                'customer_name' => 'nullable|string|max:255',
                'company_name' => 'nullable|string|max:255',
                'delivery_date' => 'nullable|string|max:255',
                'publication_permission' => 'nullable|string|max:10',
                'notes' => 'nullable|string|max:1000',
                
                // プロセスフィールド
                'order_handler_id' => 'nullable|integer',
                'image_sent_date' => 'nullable|string',
                'payment_method_id' => 'nullable|integer',
                'payment_completed_date' => 'nullable|string',
                'print_request_date' => 'nullable|string',
                'print_factory_id' => 'nullable|integer',
                'print_deadline' => 'nullable|string',
                'sewing_request_date' => 'nullable|string',
                'sewing_factory_id' => 'nullable|integer',
                'sewing_deadline' => 'nullable|string',
                'quality_check_date' => 'nullable|string',
                'shipping_date' => 'nullable|string',
            ]);
            
            // データベース更新用のデータを準備
            $updateData = [];
            
            // プロセスフィールドの直接更新
            $processFields = [
                'order_handler_id',
                'image_sent_date',
                'payment_method_id', 
                'payment_completed_date',
                'print_request_date',
                'print_factory_id',
                'print_deadline',
                'sewing_request_date',
                'sewing_factory_id',
                'sewing_deadline',
                'quality_check_date',
                'shipping_date',
                'notes'
            ];
            
            foreach ($processFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $updateData[$field] = $validated[$field];
                }
            }
            
            // 基本注文情報（content更新が必要）
            $contentFields = [
                'category',
                'customer_name',
                'company_name',
                'delivery_date',
                'publication_permission'
            ];
            
            $hasContentUpdate = false;
            foreach ($contentFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $hasContentUpdate = true;
                    break;
                }
            }
            
            // プロセスフィールドの直接更新
            if (!empty($updateData)) {
                $updateData['is_edited'] = 1;
                $updateData['edited_at'] = now();
                $updateData['edited_by'] = 'system';
                
                $order->update($updateData);
            }
            
            // 基本注文情報の更新（content JSON更新）
            if ($hasContentUpdate) {
                $contentUpdateData = array_filter($validated, function($key) use ($contentFields) {
                    return in_array($key, $contentFields);
                }, ARRAY_FILTER_USE_KEY);
                
                $order->updateOrderInfo($contentUpdateData);
                
                // 編集フラグも更新
                $order->update([
                    'is_edited' => 1,
                    'edited_at' => now(),
                    'edited_by' => 'system'
                ]);
            }
            
            // 更新後のデータを取得
            $order->refresh();
            
            // レスポンスデータ準備
            $responseData = [
                'id' => $order->id,
                'updated_at' => $order->formatted_edited ?? now()->setTimezone('Asia/Tokyo')->format('Y/m/d H:i')
            ];
            
            // 関連データの名前取得（安全にリレーションシップを呼び出す）
            try {
                if (isset($validated['order_handler_id']) && $validated['order_handler_id']) {
                    $orderHandler = $order->orderHandler;
                    $responseData['order_handler_name'] = $orderHandler ? $orderHandler->name : '未選択';
                }
                if (isset($validated['payment_method_id']) && $validated['payment_method_id']) {
                    $paymentMethod = $order->paymentMethod;
                    $responseData['payment_method_name'] = $paymentMethod ? $paymentMethod->name : '未選択';
                }
                if (isset($validated['print_factory_id']) && $validated['print_factory_id']) {
                    $printFactory = $order->printFactory;
                    $responseData['print_factory_name'] = $printFactory ? $printFactory->name : '未選択';
                }
                if (isset($validated['sewing_factory_id']) && $validated['sewing_factory_id']) {
                    $sewingFactory = $order->sewingFactory;
                    $responseData['sewing_factory_name'] = $sewingFactory ? $sewingFactory->name : '未選択';
                }
            } catch (\Exception $relationException) {
                // リレーションシップの問題があっても処理を続行
                \Illuminate\Support\Facades\Log::warning('リレーションシップエラー', ['error' => $relationException->getMessage()]);
            }
            
            return response()->json([
                'success' => true,
                'message' => '注文情報を更新しました',
                'data' => $responseData
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データが正しくありません',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '注文情報の更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders by form title.
     *
     * @param Request $request
     * @param string $formTitle
     * @return JsonResponse
     */
    public function byFormTitle(Request $request, $formTitle): JsonResponse
    {
        try {
            $query = Order::excludeInquiryAndSample()->byFormTitle($formTitle);
            
            // ソート機能
            $sortBy = $request->get('sort_by', 'created');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // ページネーション
            $perPage = $request->get('per_page', 20);
            $orders = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => "{$formTitle}の注文データを取得しました"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '注文データの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_orders' => Order::excludeInquiryAndSample()->count(),
                'quote_orders' => Order::excludeInquiryAndSample()->where('formTitle', 'like', '%見積%')->count(),
                'inquiry_orders' => Order::excludeInquiryAndSample()->where('formTitle', 'like', '%お問い合わせ%')->count(),
                'recent_orders' => Order::excludeInquiryAndSample()->where('created', '>=', Carbon::now()->subDays(7)->timestamp)->count(),
                'high_value_orders' => Order::excludeInquiryAndSample()->where('total', '>=', 50000)->count(),
                'total_value' => Order::excludeInquiryAndSample()->sum('total'),
                'average_value' => Order::excludeInquiryAndSample()->avg('total'),
                'orders_by_form' => Order::excludeInquiryAndSample()->selectRaw('formTitle, COUNT(*) as count')
                    ->groupBy('formTitle')
                    ->get(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => '統計情報を取得しました'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '統計情報の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display order images.
     *
     * @param string $orderId
     * @return \Illuminate\View\View
     */
    public function viewImages($orderId)
    {
        // uploads/{orderId}/ フォルダの画像ファイルを取得
        $uploadPath = public_path("uploads/{$orderId}");
        $images = [];
        
        if (is_dir($uploadPath)) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $files = scandir($uploadPath);
            
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($extension, $imageExtensions)) {
                    $images[] = [
                        'name' => $file,
                        'url' => asset("uploads/{$orderId}/{$file}"),
                        'path' => "uploads/{$orderId}/{$file}"
                    ];
                }
            }
        }
        
        return view('images.show', compact('orderId', 'images'));
    }
}
