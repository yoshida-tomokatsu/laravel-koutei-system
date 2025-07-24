<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_wqorders_editable';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'formId',
        'formTitle',
        'customer',
        'total',
        'created',
        'content',
        'last_updated',
        'edited_at',
        'edited_by',
        'is_edited',
        'order_handler_id',
        'image_sent_date',
        'payment_method_id',
        'payment_completed_date',
        'print_factory_id',
        'print_request_date',
        'print_deadline',
        'sewing_factory_id',
        'sewing_request_date',
        'sewing_deadline',
        'quality_check_date',
        'shipping_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
        'total' => 'decimal:2',
        'edited_at' => 'datetime',
        'image_sent_date' => 'date',
        'payment_completed_date' => 'date',
        'print_request_date' => 'date',
        'print_deadline' => 'date',
        'sewing_request_date' => 'date',
        'sewing_deadline' => 'date',
        'quality_check_date' => 'date',
        'shipping_date' => 'date',
    ];

    /**
     * Get the formatted order ID.
     *
     * @return string
     */
    public function getOrderIdAttribute()
    {
        return '#' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the customer name from JSON content.
     *
     * @return string|null
     */
    public function getCustomerNameAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['type']) && $attr['type'] === 'Name' && isset($attr['name']) && $attr['name'] === 'お名前') {
                    return $attr['value'];
                }
            }
        }
        return null;
    }

    /**
     * Get the customer email from JSON content.
     *
     * @return string|null
     */
    public function getCustomerEmailAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['type']) && $attr['type'] === 'Email') {
                    return $attr['value'];
                }
            }
        }
        return null;
    }

    /**
     * Get the customer phone from JSON content.
     *
     * @return string|null
     */
    public function getCustomerPhoneAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['type']) && $attr['type'] === 'Tel') {
                    return $attr['value'];
                }
            }
        }
        return null;
    }

    /**
     * Get the customer address from JSON content.
     *
     * @return string|null
     */
    public function getCustomerAddressAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['type']) && $attr['type'] === 'Address') {
                    return $attr['value'];
                }
            }
        }
        return null;
    }

    /**
     * Get the company name from JSON content.
     *
     * @return string|null
     */
    public function getCompanyNameAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['name']) && (strpos($attr['name'], '会社名') !== false || 
                    strpos($attr['name'], '法人名') !== false || 
                    strpos($attr['name'], '団体名') !== false)) {
                    return $attr['value'];
                }
            }
        }
        return null;
    }

    /**
     * Get the delivery hope date from JSON content.
     *
     * @return string|null
     */
    public function getDeliveryHopeDateAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['name']) && (strpos($attr['name'], 'お届け希望日') !== false || 
                    strpos($attr['name'], '納品希望日') !== false || 
                    strpos($attr['name'], '希望日') !== false)) {
                    return $attr['value'];
                }
            }
        }
        return null;
    }

    /**
     * Get the publication permission from JSON content.
     *
     * @return string|null
     */
    public function getPublicationPermissionAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['name']) && (strpos($attr['name'], '制作事例掲載許可') !== false || 
                    strpos($attr['name'], '掲載許可') !== false ||
                    strpos($attr['name'], '事例掲載') !== false)) {
                    return $attr['value'];
                }
            }
        }
        return 'しない'; // デフォルト値
    }

    /**
     * Get the remarks from JSON content.
     *
     * @return string|null
     */
    public function getRemarksFromContentAttribute()
    {
        $content = $this->content;
        if (isset($content['attrs']) && is_array($content['attrs'])) {
            foreach ($content['attrs'] as $attr) {
                if (isset($attr['name']) && isset($attr['value'])) {
                    $name = $attr['name'];
                    $value = $attr['value'];
                    
                    // 備考関連のキーワードを検索
                    if (strpos($name, '備考') !== false || 
                        strpos($name, 'コメント') !== false || 
                        strpos($name, 'メモ') !== false ||
                        strpos($name, '要望') !== false ||
                        strpos($name, '連絡') !== false ||
                        strpos($name, '特記') !== false ||
                        strpos($name, '注意') !== false ||
                        strpos($name, 'その他') !== false) {
                        
                        // 配列の場合は文字列に変換
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        
                        // HTMLエンティティをデコード
                        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
                        
                        // 改行を保持し、各行の先頭・末尾の空白を除去
                        $lines = explode("\n", $value);
                        $trimmedLines = array_map('trim', $lines);
                        $value = implode("\n", $trimmedLines);
                        
                        // 全体の先頭・末尾の空白・改行を除去
                        $value = trim($value);
                        
                        return $value;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get the formatted created date.
     *
     * @return string
     */
    public function getFormattedCreatedAttribute()
    {
        return Carbon::createFromTimestamp($this->created)->format('Y/m/d H:i');
    }

    /**
     * Get the formatted created date only.
     *
     * @return string
     */
    public function getFormattedCreatedDateAttribute()
    {
        return Carbon::createFromTimestamp($this->created)->format('Y/m/d');
    }

    /**
     * Get the formatted last updated date.
     *
     * @return string
     */
    public function getFormattedLastUpdatedAttribute()
    {
        if ($this->last_updated && $this->last_updated > 0) {
            return Carbon::createFromTimestamp($this->last_updated)->format('Y/m/d H:i');
        }
        return '-';
    }

    /**
     * Get the order status based on form title.
     *
     * @return string
     */
    public function getStatus()
    {
        if (str_contains($this->formTitle, '見積')) {
            return 'quote';
        } elseif (str_contains($this->formTitle, 'お問い合わせ')) {
            return 'inquiry';
        } else {
            return 'order';
        }
    }

    /**
     * Get the status label in Japanese.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            'quote' => '見積り',
            'inquiry' => 'お問い合わせ',
            'order' => '注文',
        ];

        return $statusLabels[$this->getStatus()] ?? 'その他';
    }

    /**
     * Get the category color based on form title.
     *
     * @return string
     */
    public function getCategoryColor()
    {
        if (str_contains($this->formTitle, '見積')) {
            return '#4CAF50'; // 緑色
        } elseif (str_contains($this->formTitle, 'お問い合わせ')) {
            return '#2196F3'; // 青色
        } else {
            return '#FF9800'; // オレンジ色
        }
    }

    /**
     * Get the PDF path if exists.
     *
     * @return string|null
     */
    public function getPdfPath()
    {
        // Extract numeric order ID from the formatted order_id (#0001 -> 1)
        $numericOrderId = (int) str_replace('#', '', $this->order_id);
        
        // Determine folder based on order ID range
        $folder = $this->determinePdfFolder($numericOrderId);
        
        // Format order ID for filename (should be 5 digits with leading zeros)
        $orderIdPadded = str_pad($numericOrderId, 5, '0', STR_PAD_LEFT);
        
        // Check for PDF in the determined folder
        $pdfPath = "/aforms-pdf/{$folder}/{$orderIdPadded}.pdf";
        
        if (file_exists(public_path($pdfPath))) {
            return $pdfPath;
        }
        
        // Also check tmp folder for temporary PDFs
        $tmpPath = "/aforms-pdf/tmp/";
        if (is_dir(public_path($tmpPath))) {
            $tmpDirs = scandir(public_path($tmpPath));
            foreach ($tmpDirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir(public_path($tmpPath . $dir))) {
                    $quotePath = $tmpPath . $dir . '/quote.pdf';
                    if (file_exists(public_path($quotePath))) {
                        return $quotePath;
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Determine PDF folder based on order ID range.
     *
     * @param int $orderId
     * @return string
     */
    public function determinePdfFolder($orderId)
    {
        if ($orderId >= 483 && $orderId <= 999) {
            return '01-000';
        } elseif ($orderId >= 1001 && $orderId <= 1313) {
            return '01-001';
        } elseif ($orderId >= 2000 && $orderId <= 2999) {
            return '01-002'; // Future folder for 2000 series
        } else {
            // Default to 01-000 for unknown ranges
            return '01-000';
        }
    }
    
    /**
     * Get all PDF files for this order (ID不一致問題対応版).
     *
     * @return array
     */
    public function getPdfFiles()
    {
        $pdfs = [];
        $numericOrderId = (int) str_replace('#', '', $this->order_id);
        $folders = ['01-000', '01-001', '01-002'];
        
        // 複数の検索戦略を使用
        $searchPatterns = [
            str_pad($numericOrderId, 5, '0', STR_PAD_LEFT),  // 5桁パディング
            str_pad($numericOrderId, 4, '0', STR_PAD_LEFT),  // 4桁パディング
            $numericOrderId                                   // パディングなし
        ];
        
        foreach ($folders as $folder) {
            $folderPath = public_path("aforms-pdf/{$folder}");
            if (!is_dir($folderPath)) {
                continue;
            }
            
            // 直接検索
            foreach ($searchPatterns as $pattern) {
                $basePath = "{$folderPath}/{$pattern}";
                
                // メインファイル
                if (file_exists("{$basePath}.pdf")) {
                    $pdfs[] = $this->createPdfFileInfo("{$pattern}.pdf", $folder, 'main');
                }
                
                // 連番ファイル (_1, _2, etc.)
                $counter = 1;
                while (file_exists("{$basePath}_{$counter}.pdf")) {
                    $pdfs[] = $this->createPdfFileInfo("{$pattern}_{$counter}.pdf", $folder, 'additional');
                    $counter++;
                }
            }
            
            // 部分文字列検索（上記で見つからない場合）
            if (empty($pdfs)) {
                $allFiles = scandir($folderPath);
                foreach ($allFiles as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') {
                        continue;
                    }
                    
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    
                    // ファイル名に注文番号が含まれているかチェック
                    foreach ($searchPatterns as $pattern) {
                        if (strpos($filename, $pattern) !== false) {
                            $pdfs[] = $this->createPdfFileInfo($file, $folder, 'main');
                            break;
                        }
                    }
                }
            }
        }
        
        // フォールバック: 利用可能な最初のPDFファイルを返す（テスト用）
        if (empty($pdfs)) {
            foreach ($folders as $folder) {
                $folderPath = public_path("aforms-pdf/{$folder}");
                if (is_dir($folderPath)) {
                    $files = scandir($folderPath);
                    foreach ($files as $file) {
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                            $pdfs[] = $this->createPdfFileInfo($file, $folder, 'fallback');
                            break 2; // 最初の1つだけ
                        }
                    }
                }
            }
        }
        
        return $this->sortPdfFiles($pdfs, $numericOrderId);
    }
    
    /**
     * Create PDF file info array.
     *
     * @param string $filename
     * @param string $folder
     * @param string $type
     * @return array
     */
    private function createPdfFileInfo($filename, $folder, $type = 'main')
    {
        $fullPath = public_path("aforms-pdf/{$folder}/{$filename}");
        
        return [
            'name' => $filename,
            'path' => "/aforms-pdf/{$folder}/{$filename}",
            'type' => $type,
            'created_time' => file_exists($fullPath) ? filemtime($fullPath) : 0,
            'display_order' => $type === 'main' ? 0 : 1,
            'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
            'url' => asset("aforms-pdf/{$folder}/{$filename}")
        ];
    }
    
    /**
     * Sort PDF files by order info.
     *
     * @param array $files
     * @param string $orderNumber
     * @return array
     */
    private function sortPdfFiles($files, $orderNumber)
    {
        if (empty($files)) {
            return $files;
        }
        
        // 順序情報ファイルを確認
        $orderIdPadded = str_pad($orderNumber, 5, '0', STR_PAD_LEFT);
        
        // フォルダごとに順序ファイルをチェック
        $orderData = [];
        foreach (['01-000', '01-001', '01-002'] as $folder) {
            $orderFile = public_path("aforms-pdf/{$folder}/{$orderIdPadded}_order.json");
            if (file_exists($orderFile)) {
                $orderData = array_merge($orderData, json_decode(file_get_contents($orderFile), true) ?: []);
            }
        }
        
        if (!empty($orderData)) {
            usort($files, function($a, $b) use ($orderData) {
                $orderA = $orderData[$a['name']] ?? 999;
                $orderB = $orderData[$b['name']] ?? 999;
                return $orderA - $orderB;
            });
        } else {
            // デフォルトソート（メインファイル優先、その後作成日時順）
            usort($files, function($a, $b) {
                if ($a['type'] === 'main' && $b['type'] !== 'main') {
                    return -1;
                }
                if ($a['type'] !== 'main' && $b['type'] === 'main') {
                    return 1;
                }
                return $a['created_time'] - $b['created_time'];
            });
        }
        
        return $files;
    }
    
    /**
     * Get display order for a PDF file based on its name.
     *
     * @param string $filename
     * @param string $orderIdPadded
     * @return int
     */
    private function getDisplayOrder($filename, $orderIdPadded)
    {
        // メインファイルは順序0
        if ($filename === "{$orderIdPadded}.pdf") {
            return 0;
        }
        
        // 連番ファイルの場合は連番を抽出
        if (preg_match('/^' . preg_quote($orderIdPadded, '/') . '_(\d+)\.pdf$/', $filename, $matches)) {
            return (int)$matches[1];
        }
        
        // その他のファイルは大きな値
        return 999;
    }

    /**
     * Check if order has PDF.
     *
     * @return bool
     */
    public function hasPdf()
    {
        return !is_null($this->getPdfPath());
    }

    /**
     * Check if order has images.
     *
     * @return bool
     */
    public function hasImages()
    {
        // uploads/{order_id}/ フォルダに画像ファイルが存在するかチェック
        $uploadPath = public_path("uploads/{$this->order_id}");
        
        if (!is_dir($uploadPath)) {
            return false;
        }
        
        // 画像ファイルの拡張子
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $files = scandir($uploadPath);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $imageExtensions)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get days since order creation.
     *
     * @return int
     */
    public function getDaysSinceCreation()
    {
        return Carbon::createFromTimestamp($this->created)->diffInDays(Carbon::now());
    }

    /**
     * Check if order is recent (within 7 days).
     *
     * @return bool
     */
    public function isRecent()
    {
        return $this->getDaysSinceCreation() <= 7;
    }

    /**
     * Scope a query to filter by form title.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $formTitle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFormTitle($query, $formTitle)
    {
        return $query->where('formTitle', 'like', "%{$formTitle}%");
    }

    /**
     * Scope a query to filter by total amount range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $min
     * @param  float  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTotalRange($query, $min, $max)
    {
        return $query->whereBetween('total', [$min, $max]);
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $from
     * @param  int  $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('created', [$from, $to]);
    }

    /**
     * Scope a query to exclude inquiry and sample request records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExcludeInquiryAndSample($query)
    {
        return $query->whereNotIn('formTitle', ['お問い合わせ', 'サンプル請求']);
    }

    /**
     * Get the product category based on formTitle.
     *
     * @return string
     */
    public function getProductCategory()
    {
        $formTitle = $this->formTitle;
        
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

    /**
     * Get the category color for product category.
     *
     * @return string
     */
    public function getProductCategoryColor()
    {
        $category = $this->getProductCategory();
        
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

    /**
     * Get all available product categories.
     *
     * @return array
     */
    public static function getProductCategories()
    {
        return [
            'ポリエステル スカーフ',
            'シルク スカーフ',
            'リボン スカーフ',
            'スカーフタイ',
            'ストール',
            'ポケットチーフ'
        ];
    }

    /**
     * Update order information.
     *
     * @param array $data
     * @return bool
     */
    public function updateOrderInfo($data)
    {
        try {
            $content = $this->content;
            $updated = false;

            // ジャンル更新
            if (isset($data['category'])) {
                $this->formTitle = $data['category'];
                $updated = true;
            }

            // 注文者更新
            if (isset($data['customer_name'])) {
                $content = $this->updateContentField($content, 'Name', 'お名前', $data['customer_name']);
                $updated = true;
            }

            // 会社名更新
            if (isset($data['company_name'])) {
                $content = $this->updateContentField($content, 'Text', '会社名', $data['company_name']);
                $updated = true;
            }

            // 納期更新
            if (isset($data['delivery_date'])) {
                $content = $this->updateContentField($content, 'Date', 'お届け希望日', $data['delivery_date']);
                $updated = true;
            }

            // 制作事例掲載許可更新
            if (isset($data['publication_permission'])) {
                $content = $this->updateContentField($content, 'Radio', '制作事例掲載許可', $data['publication_permission']);
                $updated = true;
            }

            if ($updated) {
                $this->content = $content;
                $this->is_edited = 1;
                $this->edited_at = now();
                $this->edited_by = 'system'; // 将来的にはユーザー情報を入れる
                return $this->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update content field in JSON.
     *
     * @param array $content
     * @param string $type
     * @param string $name
     * @param string $value
     * @return array
     */
    private function updateContentField($content, $type, $name, $value)
    {
        if (!isset($content['attrs']) || !is_array($content['attrs'])) {
            $content['attrs'] = [];
        }

        $found = false;
        foreach ($content['attrs'] as &$attr) {
            if (isset($attr['name']) && strpos($attr['name'], $name) !== false) {
                $attr['value'] = $value;
                $found = true;
                break;
            }
        }

        // 新しいフィールドとして追加
        if (!$found) {
            $content['attrs'][] = [
                'type' => $type,
                'name' => $name,
                'value' => $value
            ];
        }

        return $content;
    }

    /**
     * Get publication permission options.
     *
     * @return array
     */
    public static function getPublicationPermissionOptions()
    {
        return [
            'しない' => 'しない',
            'する' => 'する'
        ];
    }

    /**
     * Get formatted edited date.
     *
     * @return string|null
     */
    public function getFormattedEditedAttribute()
    {
        if ($this->edited_at) {
            // edited_atがCarbonオブジェクトの場合
            if ($this->edited_at instanceof \Carbon\Carbon) {
                return $this->edited_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i');
            }
            
            // edited_atが文字列の場合はCarbonに変換
            if (is_string($this->edited_at)) {
                try {
                    return \Carbon\Carbon::parse($this->edited_at)->setTimezone('Asia/Tokyo')->format('Y/m/d H:i');
                } catch (\Exception $e) {
                    return $this->edited_at; // パースできない場合は元の文字列を返す
                }
            }
        }
        return null;
    }

    /**
     * Get the order handler for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderHandler()
    {
        return $this->belongsTo(OrderHandler::class, 'order_handler_id');
    }

    /**
     * Get the payment method for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    /**
     * Get the print factory for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function printFactory()
    {
        return $this->belongsTo(PrintFactory::class, 'print_factory_id');
    }

    /**
     * Get the sewing factory for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sewingFactory()
    {
        return $this->belongsTo(SewingFactory::class, 'sewing_factory_id');
    }

    /**
     * Get formatted date field.
     *
     * @param string $field
     * @return string|null
     */
    public function getFormattedDate($field)
    {
        if ($this->$field) {
            return $this->$field->format('Y/m/d');
        }
        return null;
    }

    /**
     * Get all process status.
     *
     * @return array
     */
    public function getProcessStatus()
    {
        return [
            'image_sent' => !is_null($this->image_sent_date),
            'payment_completed' => !is_null($this->payment_completed_date),
            'print_requested' => !is_null($this->print_request_date),
            'sewing_requested' => !is_null($this->sewing_request_date),
            'quality_checked' => !is_null($this->quality_check_date),
            'shipped' => !is_null($this->shipping_date),
        ];
    }
    
    /**
     * Create PDF file info array.
     *
     * @param string $filename
     * @param string $folder
     * @return array
     */
    private function createPdfFileInfo($filename, $folder)
    {
        $fullPath = public_path("aforms-pdf/{$folder}/{$filename}");
        
        return [
            'name' => $filename,
            'filename' => $filename,
            'path' => "/aforms-pdf/{$folder}/{$filename}",
            'full_path' => $fullPath,
            'folder' => $folder,
            'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
            'url' => asset("aforms-pdf/{$folder}/{$filename}"),
            'type' => 'pdf'
        ];
    }
    
    /**
     * Sort PDF files by order info.
     *
     * @param array $files
     * @param string $orderNumber
     * @return array
     */
    private function sortPdfFiles($files, $orderNumber)
    {
        if (empty($files)) {
            return $files;
        }
        
        // 順序情報ファイルを確認
        $folder = pathinfo($files[0]['path'], PATHINFO_DIRNAME);
        $orderIdPadded = str_pad($orderNumber, 5, '0', STR_PAD_LEFT);
        $orderFile = public_path("aforms-pdf/{$files[0]['folder']}/{$orderIdPadded}_order.json");
        
        if (file_exists($orderFile)) {
            $orderData = json_decode(file_get_contents($orderFile), true) ?: [];
            
            usort($files, function($a, $b) use ($orderData) {
                $orderA = $orderData[$a['name']] ?? 999;
                $orderB = $orderData[$b['name']] ?? 999;
                return $orderA - $orderB;
            });
        }
        
        return $files;
    }
}
