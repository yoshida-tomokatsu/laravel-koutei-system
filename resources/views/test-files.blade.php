<!DOCTYPE html>
<html>
<head>
    <title>ファイル管理機能テスト</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #4a90e2;
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .file-column {
            width: 80px;
            text-align: center;
        }
        .file-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 5px;
        }
        .file-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .file-label {
            font-size: 9px;
            color: #666;
        }
        .file-button {
            width: 70px;
            height: 20px;
            font-size: 8px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .file-button.quote {
            background: #28a745;
            color: white;
        }
        .file-button.image {
            background: #007bff;
            color: white;
        }
        .file-button:hover {
            opacity: 0.8;
        }
        .no-files {
            font-size: 8px;
            color: #999;
            font-style: italic;
        }
        .order-info {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .category-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .category-high { background: #dc3545; }
        .category-medium { background: #ffc107; color: #000; }
        .category-low { background: #28a745; }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        .status-pending { background: #6c757d; }
        .status-in_progress { background: #007bff; }
        .status-completed { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ファイル管理機能テスト</h1>
            <p>認証なしでファイル管理機能をテストできます</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>顧客名</th>
                        <th>会社名</th>
                        <th>注文日</th>
                        <th>カテゴリ</th>
                        <th>ステータス</th>
                        <th class="file-column">ファイル</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->company_name }}</td>
                        <td>{{ $order->order_date }}</td>
                        <td>
                            <span class="category-badge category-{{ $order->category }}">
                                {{ $order->category }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="file-column">
                            <div class="file-cell">
                                <!-- 見積もりセクション -->
                                <div class="file-section">
                                    <div class="file-label">見積</div>
                                    @if($order->id == 1)
                                        <button class="file-button quote" onclick="viewQuotePdf({{ $order->id }})">
                                            ファイル選択
                                        </button>
                                    @else
                                        <div class="no-files">ファイルなし</div>
                                    @endif
                                </div>
                                
                                <!-- 画像セクション -->
                                <div class="file-section">
                                    <div class="file-label">📷画像</div>
                                    @if($order->id == 1)
                                        <button class="file-button image" onclick="viewOrderImages({{ $order->id }})">
                                            ファイル選択
                                        </button>
                                    @else
                                        <div class="no-files">ファイルなし</div>
                                    @endif
                                </div>
                                
                                <!-- 注文情報 -->
                                <div class="order-info">
                                    注文日時：{{ $order->order_date }}<br>
                                    更新日時：-
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function viewQuotePdf(orderId) {
            alert('PDF表示機能: 注文ID ' + orderId + ' の見積もりPDFを表示します');
            // 実際の実装では、PDFビューアーを開く
            // window.open('/pdf/' + orderId, '_blank');
        }

        function viewOrderImages(orderId) {
            alert('画像表示機能: 注文ID ' + orderId + ' の画像を表示します');
            // 実際の実装では、画像ギャラリーを開く
            window.open('/test-images/' + orderId, '_blank');
        }
    </script>
</body>
</html> 