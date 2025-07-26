<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工程管理システム - Laravel版</title>
    
    <!-- セキュリティ強化メタタグ -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SortableJS Library for reliable drag & drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"></script>
    
    <!-- 工程管理システム専用スタイル -->
    <style>
        /* 基本設定 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100vh;
            overflow: hidden;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans JP", Roboto, Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            font-size: 13px;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ヘッダー */
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
            flex-shrink: 0;
            min-width: 1200px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 500;
            margin: 0;
        }

        .header-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-new {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            transition: background-color 0.2s;
        }

        .btn-new:hover {
            background-color: #2980b9;
        }

        /* 詳細検索ボタン */
        .btn-detail-search {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .btn-detail-search:hover {
            background: #2980b9;
        }

        /* 表示モード切り替え */
        .view-mode-toggle {
            display: flex;
            gap: 2px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .view-mode-btn {
            padding: 6px 12px;
            border: none;
            background-color: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border-right: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
        }

        .view-mode-btn:last-child {
            border-right: none;
        }

        .view-mode-btn:hover {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .view-mode-btn.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }

        /* タブナビゲーション */
        .tab-navigation-compact {
            display: flex;
            gap: 2px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .tab-button-compact {
            padding: 6px 12px;
            border: none;
            background-color: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-button-compact:hover {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .tab-button-compact.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }

        /* 管理者ボタン */
        .admin-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-controls .btn-new:hover {
            background-color: #8e44ad !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(155, 89, 182, 0.3);
        }

        /* メインコンテンツ */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .container {
            padding: 20px;
            position: relative;
            height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

                 /* 統計情報 */
         .pagination-container {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-bottom: 20px;
             padding: 10px;
             background-color: #f8f9fa;
             border-radius: 4px;
             flex-shrink: 0;
         }

         .display-controls {
             display: flex;
             align-items: center;
             gap: 10px;
         }

         .per-page-selector {
             display: flex;
             align-items: center;
         }

        /* プロセステーブル */
        .process-table {
            width: 100%;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: auto;
            flex: 1;
            min-height: 400px;
        }

        .process-table::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .process-table::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 4px;
        }

        .process-table::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .process-table::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

                 table {
             width: 100%;
             border-collapse: collapse;
             font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
             min-width: 1800px;
         }

        thead {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .table-header {
            background-color: #34495e;
        }

        .table-header th {
            background-color: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            border: 1px solid #2c3e50;
        }

        .process-header {
            background-color: #34495e;
        }

        .process-header th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: 500;
            font-size: 11px;
            border: 1px solid #2c3e50;
        }

        /* プロセス別の色分け */
        .process-order { 
            background-color: #3498db !important; 
            color: white !important; 
        }
        .process-print { 
            background-color: #2ecc71 !important; 
            color: white !important; 
        }
        .process-sewing { 
            background-color: #f39c12 !important; 
            color: white !important; 
        }
        .process-inspection { 
            background-color: #e74c3c !important; 
            color: white !important; 
        }

        /* データ行 */
        .order-row {
            border-bottom: 2px solid #ecf0f1;
            transition: background-color 0.2s;
        }

        .order-row:hover {
            background-color: #f8f9fa;
        }

        .order-row td {
            padding: 12px 10px;
            vertical-align: middle;
            border-right: 1px solid #ecf0f1;
            font-size: 13px;
        }

        .order-row td:last-child {
            border-right: none;
        }

        /* 行番号 */
        .row-number {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            text-align: center;
            width: 60px;
        }

        /* 注文情報セル */
        .order-info {
            min-width: 320px;
            margin-bottom: 16px;
            background-color: #f8f9fa;
        }

        .order-info .order-header {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: nowrap !important;
            margin-bottom: 6px !important;
        }

        .order-info .order-number {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-weight: bold !important;
            color: #2c3e50;
            font-size: 13px;
        }

        .order-number {
            color: #2c3e50;
            font-size: 13px;
        }

        .order-info .client-name,
        .order-info .company-name,
        .order-info .publication-permission {
            font-weight: normal !important;
        }

        .client-name {
            color: #7f8c8d;
            margin-top: 4px;
        }

        .company-name {
            color: #7f8c8d;
            margin-top: 4px;
        }

        .order-date {
            color: #3498db;
            font-size: 12px;
            margin-top: 2px;
        }

        .delivery-date {
            color: #7f8c8d;
            font-size: 16px;
            margin-top: 2px;
            margin-bottom: 8px;
        }

        /* カテゴリドロップダウン */
        .category-dropdown {
            flex: 1 !important;
            min-width: 0 !important;
            max-width: none !important;
            font-size: 12px;
            padding: 2px 4px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .category-poli { background-color: #3498db; color: white; }
        .category-silk { background-color: #2ecc71; color: white; }
        .category-ribbon { background-color: #e67e22; color: white; }
        .category-tie { background-color: #663399; color: white; }
        .category-stole { background-color: #e74c3c; color: white; }
        .category-chief { background-color: #95a5a6; color: white; }

        /* ファイル管理 */
        .file-management-cell {
            background-color: #f8f9fa;
            width: 80px;
            min-width: 80px;
            text-align: center;
            font-size: 10px;
            padding: 6px 3px;
            vertical-align: top;
        }

        .file-section {
            margin-bottom: 8px;
        }

        .file-section:last-child {
            margin-bottom: 0;
        }

        .btn-file-select {
            transition: all 0.2s ease;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 3px;
            border: none;
            cursor: pointer;
            font-size: 10px;
            width: 100%;
            margin-bottom: 2px;
        }

        .btn-quote-pdf {
            background-color: #28a745;
            color: white;
        }

        .btn-quote-pdf:hover {
            background-color: #218838;
        }
        
        .btn-quote-manage {
            background-color: #fd7e14;
            color: white;
        }
        
        .btn-quote-manage:hover {
            background-color: #e8590c;
        }
        
        .btn-quote-upload {
            background-color: #0d6efd;
            color: white;
        }
        
        .btn-quote-upload:hover {
            background-color: #0b5ed7;
        }
        
        .quote-buttons {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .quote-buttons .btn-file-select {
            font-size: 9px;
            padding: 2px 4px;
            white-space: nowrap;
        }

        .btn-image-view {
            background-color: #6f42c1;
            color: white;
        }

        .btn-image-view:hover {
            background-color: #5a2d91;
        }

        .btn-file-select-disabled {
            background-color: #6c757d;
            color: white;
        }

        .btn-file-select-disabled:hover {
            background-color: #5a6268;
        }

        /* 編集可能フィールド */
        .editable-cell {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .editable-cell:hover {
            background-color: #e3f2fd;
        }

        .editable-field {
            width: 100%;
            padding: 2px 4px;
            border: 1px solid #ddd;
            border-radius: 2px;
            font-size: 12px;
        }

        /* 編集ボタン */
        .action-buttons {
            margin-top: 6px;
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .btn-edit-order,
        .btn-save-order,
        .btn-cancel-order {
            padding: 4px 8px;
            border: none;
            border-radius: 3px;
            font-size: 11px;
            cursor: pointer;
            min-width: 50px;
        }

        .btn-edit-order {
            background-color: #6c757d;
            color: white;
        }

        .btn-save-order {
            background-color: #28a745;
            color: white;
        }

        .btn-cancel-order {
            background-color: #dc3545;
            color: white;
        }

        .btn-order-response {
            background-color: #ff8c00;
            color: white;
            padding: 4px 8px;
            border: none;
            border-radius: 3px;
            font-size: 11px;
            cursor: pointer;
        }

        /* 備考セル */
        .remarks {
            width: 400px;
            min-width: 400px;
            max-width: 500px;
            word-wrap: break-word;
            white-space: pre-line; /* 改行を保持、連続スペースは1つにまとめる */
            vertical-align: top;
        }

                 /* プロセス情報 */
         .process-info {
             text-align: center;
             min-width: 120px;
             padding: 8px 12px;
         }

        .date-input {
            width: 100%;
            padding: 2px 4px;
            border: 1px solid #ddd;
            border-radius: 2px;
            font-size: 11px;
            text-align: center;
        }

                 /* 各種セレクト */
         .person-select, 
         .factory-select, 
         .payment-select, 
         .shipping-select {
             width: 100%;
             font-size: 11px;
             padding: 2px 4px;
         }

         /* プロセスフィールド自動保存用 */
         .process-input, .process-select {
             width: 100%;
             padding: 4px 8px;
             border: 1px solid #ddd;
             border-radius: 3px;
             font-size: 12px;
             transition: all 0.2s;
             background-color: white;
         }

         .process-input:focus, .process-select:focus {
             border-color: #3498db;
             outline: none;
             box-shadow: 0 0 3px rgba(52, 152, 219, 0.3);
         }

         .auto-save-field {
             position: relative;
         }

         .auto-save-field.saving {
             opacity: 0.7;
         }

         .auto-save-field.saving::after {
             content: '保存中...';
             position: absolute;
             top: 50%;
             left: 50%;
             transform: translate(-50%, -50%);
             background-color: rgba(52, 152, 219, 0.9);
             color: white;
             padding: 2px 6px;
             border-radius: 3px;
             font-size: 10px;
             z-index: 1000;
         }

        /* ローディング */
        .loading {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }

        /* SortableJS スタイル */
        .sortable-ghost {
            opacity: 0.4;
            background: #f8f9fa !important;
        }

        .sortable-chosen {
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5) !important;
            border-color: #3498db !important;
        }

        .sortable-drag {
            transform: rotate(5deg);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3) !important;
        }

                 /* カスタムページネーション */
         .custom-pagination {
             display: flex;
             justify-content: center;
             align-items: center;
             gap: 2px;
             flex-wrap: wrap;
         }

         .page-btn {
             padding: 6px 12px;
             border: 1px solid #ddd;
             background-color: white;
             color: #495057;
             text-decoration: none;
             border-radius: 4px;
             font-size: 14px;
             transition: all 0.2s;
             min-width: 40px;
             text-align: center;
         }

         .page-btn:hover {
             background-color: #f8f9fa;
             border-color: #adb5bd;
             color: #495057;
         }

         .page-btn.current {
             background-color: #2c3e50;
             color: white;
             border-color: #2c3e50;
         }

         .page-btn.disabled {
             color: #6c757d;
             background-color: #e9ecef;
             border-color: #dee2e6;
             cursor: not-allowed;
         }

         /* フッターページネーション */
         .footer-pagination-container {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-top: 20px;
             padding: 15px 20px;
             background-color: #f8f9fa;
             border-radius: 4px;
             border-top: 2px solid #2c3e50;
         }

         .footer-pagination-info {
             display: flex;
             align-items: center;
         }

         /* レスポンシブ対応 */
         @media (max-width: 1024px) {
             .admin-controls {
                 margin-left: 10px;
             }
             
             .admin-controls .btn-new {
                 font-size: 10px;
                 padding: 4px 8px;
             }
         }

         @media (max-width: 768px) {
             .header-controls {
                 flex-wrap: wrap;
                 gap: 10px;
             }
             
             .admin-controls {
                 order: 2;
                 width: 100%;
                 justify-content: center;
                 margin: 10px 0 0 0;
             }

             .custom-pagination {
                 gap: 1px;
             }

             .page-btn {
                 padding: 4px 8px;
                 font-size: 12px;
                 min-width: 32px;
             }

             .footer-pagination-container {
                 flex-direction: column;
                 gap: 10px;
                 text-align: center;
             }

                              .footer-pagination-info {
                     order: 2;
                 }
                 
                 .display-controls {
                     flex-direction: column;
                     gap: 5px;
                     text-align: center;
                 }

                 .per-page-selector {
                     order: 1;
                 }
             }
    </style>
</head>
<body>
    <!-- ヘッダー -->
    <div class="header">
        <h1>工程管理システム <span style="font-size: 12px; color: #bdc3c7;">Laravel版</span></h1>
        <div class="header-controls">
            <!-- 詳細検索ボタン -->
            <button id="detailSearchBtn" class="btn-detail-search">詳細検索</button>
            
            <!-- 表示モード切り替え -->
            <div class="view-mode-toggle">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'detailed']) }}" class="view-mode-btn {{ $viewMode === 'detailed' ? 'active' : '' }}">詳細表示</a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'simple']) }}" class="view-mode-btn {{ $viewMode === 'simple' ? 'active' : '' }}">簡易表示</a>
            </div>
            
            <!-- タブナビゲーション -->
            <div class="tab-navigation-compact">
                <button class="tab-button-compact {{ $currentTab === 'all' ? 'active' : '' }}" onclick="changeTab('all')">すべて</button>
                <button class="tab-button-compact {{ $currentTab === 'in-progress' ? 'active' : '' }}" onclick="changeTab('in-progress')">進行中</button>
                <button class="tab-button-compact {{ $currentTab === 'completed' ? 'active' : '' }}" onclick="changeTab('completed')">完了</button>
            </div>
            
            <!-- 管理者専用ボタン -->
            @if(true) {{-- 一時的に全員にアクセス許可 --}}
                <div class="admin-controls">
                    <button class="btn-new" onclick="showNewOrderModal()" style="background: #9b59b6;">+ 新規注文</button>
                </div>
            @endif
            
            <!-- ユーザー情報とログアウト -->
            <div style="display: flex; align-items: center; gap: 15px; margin-left: 15px;">
                <span style="color: #bdc3c7; font-size: 13px;">
                    ログイン中: <strong style="color: white;">{{ auth()->user()->user_id ?? 'ゲスト' }}</strong>
                </span>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="
                        background: #e74c3c;
                        color: white;
                        border: none;
                        padding: 6px 16px;
                        border-radius: 5px;
                        cursor: pointer;
                        font-size: 13px;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#c0392b'" onmouseout="this.style.backgroundColor='#e74c3c'">
                        ログアウト
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- メインコンテンツ -->
    <div class="main-content">
        <div class="container">
            <!-- データベースエラー情報 -->
            @if(isset($databaseError) && $databaseError)
                <div class="database-error-alert" style="
                    background-color: #fff3cd;
                    border: 1px solid #ffeaa7;
                    color: #856404;
                    padding: 15px;
                    margin-bottom: 20px;
                    border-radius: 5px;
                    position: relative;
                ">
                    <h4 style="margin: 0 0 10px 0; color: #856404;">
                        <strong>⚠️ データベース接続エラー</strong>
                    </h4>
                    <p style="margin: 0 0 10px 0;">
                        現在、本番データベースへの接続に問題があるため、テスト用のダミーデータを表示しています。
                    </p>
                </div>
            @endif

            <!-- 統計情報 -->
            <div class="pagination-container">
                <span style="font-size: 14px; color: #007bff; font-weight: 600; background-color: #e3f2fd; padding: 4px 8px; border-radius: 4px;">
                    総件数: {{ $orders->total() }}件
                </span>
                
                <div class="display-controls">
                    <span style="font-size: 13px; color: #495057; margin-right: 15px;">
                        {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }}件 / {{ $orders->total() }}件
                    </span>
                    
                    <div class="per-page-selector">
                        <label for="perPageSelect" style="font-size: 13px; color: #495057; margin-right: 5px;">表示件数:</label>
                        <select id="perPageSelect" onchange="changePerPage(this.value)" style="
                            padding: 4px 8px;
                            border: 1px solid #ddd;
                            border-radius: 3px;
                            font-size: 13px;
                            background-color: white;
                        ">
                            <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5件</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10件</option>
                            <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20件</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50件</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100件</option>
                        </select>
                    </div>
                </div>
                
                <!-- カスタムページネーション -->
                @if($orders->hasPages())
                    <div class="custom-pagination">
                        @if($orders->onFirstPage())
                            <span class="page-btn disabled">‹‹</span>
                            <span class="page-btn disabled">‹</span>
                        @else
                            <a href="{{ $orders->url(1) }}" class="page-btn">‹‹</a>
                            <a href="{{ $orders->previousPageUrl() }}" class="page-btn">‹</a>
                        @endif
                        
                        @php
                            $current = $orders->currentPage();
                            $last = $orders->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $orders->url(1) }}" class="page-btn">1</a>
                            @if($start > 2)
                                <span class="page-btn disabled">...</span>
                            @endif
                        @endif
                        
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $current)
                                <span class="page-btn current">{{ $page }}</span>
                            @else
                                <a href="{{ $orders->url($page) }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endfor
                        
                        @if($end < $last)
                            @if($end < $last - 1)
                                <span class="page-btn disabled">...</span>
                            @endif
                            <a href="{{ $orders->url($last) }}" class="page-btn">{{ $last }}</a>
                        @endif
                        
                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="page-btn">›</a>
                            <a href="{{ $orders->url($orders->lastPage()) }}" class="page-btn">››</a>
                        @else
                            <span class="page-btn disabled">›</span>
                            <span class="page-btn disabled">››</span>
                        @endif
                    </div>
                @endif
            </div>

            @if($viewMode === 'detailed')
                <!-- 詳細表示テーブル -->
                <div class="process-table detailed-view">
                    <table>
                        <!-- メインヘッダー -->
                        <thead>
                            <tr class="table-header">
                                <th rowspan="2" style="width: 60px;">No.</th>
                                <th rowspan="2" style="width: 320px;">注文情報</th>
                                <th rowspan="2" style="width: 120px;">ファイル</th>
                                <th colspan="4" class="process-order">注文対応</th>
                                <th colspan="3" class="process-print">プリント</th>
                                <th colspan="3" class="process-sewing">縫製</th>
                                <th colspan="2" class="process-inspection">検品・発送</th>
                                <th rowspan="2" style="width: 400px;">備考</th>
                            </tr>
                            <tr class="process-header">
                                <th style="width: 120px;">注文担当</th>
                                <th style="width: 120px;">イメージ送付</th>
                                <th style="width: 120px;">支払い方法</th>
                                <th style="width: 120px;">支払い完了</th>
                                <th style="width: 120px;">プリント依頼日</th>
                                <th style="width: 120px;">プリント工場</th>
                                <th style="width: 120px;">プリント納期</th>
                                <th style="width: 120px;">縫製依頼日</th>
                                <th style="width: 120px;">縫製工場</th>
                                <th style="width: 120px;">縫製納期</th>
                                <th style="width: 120px;">検品担当</th>
                                <th style="width: 120px;">発送日<br>配送会社</th>
                            </tr>
                        </thead>
                        
                        <!-- データ行 -->
                        <tbody>
                            @forelse($orders as $index => $order)
                                <tr class="order-row" data-order-id="{{ $order->id }}">
                                    <td class="row-number">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td class="order-info">
                                                                <div class="order-header">
                            <span class="order-number">注文ID：{{ $order->order_id }}</span>
                            <span class="category-display category-{{ strtolower(str_replace(' ', '', $order->getProductCategory())) }}">
                                {{ $order->getProductCategory() }}
                            </span>
                            <select class="editable-field category-dropdown category-{{ strtolower(str_replace(' ', '', $order->getProductCategory())) }}" data-original-value="{{ $order->getProductCategory() }}" onchange="updateProductCategory('{{ $order->id }}', this.value)" style="display: none;">
                                @foreach(\App\Models\Order::getProductCategories() as $category)
                                    <option value="{{ $category }}" {{ $order->getProductCategory() === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                                        <div class="order-date">注文日時：{{ $order->formatted_created ?? '未設定' }}</div>
                                        <div class="order-date">更新日時：{{ $order->formatted_edited ?? '-' }}</div>
                                        
                                        <div class="client-name">
                                            注文者：
                                            <span class="customer-name-display">{{ $order->customer_name }}</span>
                                            <input type="text" class="editable-field customer-name-input" value="{{ $order->customer_name }}" style="display: none;">
                                        </div>
                                        
                                        <div class="company-name">
                                            会社名：
                                            <span class="company-name-display">{{ $order->company_name }}</span>
                                            <input type="text" class="editable-field company-name-input" value="{{ $order->company_name }}" style="display: none;">
                                        </div>
                                        
                                        <div class="delivery-date">
                                            納期：
                                            <span class="delivery-date-display">{{ $order->delivery_hope_date }}</span>
                                            <input type="text" class="editable-field delivery-date-input" value="{{ $order->delivery_hope_date }}" placeholder="例: 2025年7月20日" style="display: none;">
                                        </div>
                                        
                                        <div class="client-name">
                                            制作事例：
                                            <span class="publication-permission-display">
                                                {{ \App\Models\Order::getPublicationPermissionOptions()[$order->publication_permission] ?? $order->publication_permission }}
                                            </span>
                                            <select class="editable-field publication-permission-select" style="display: none;">
                                                @foreach(\App\Models\Order::getPublicationPermissionOptions() as $value => $label)
                                                    <option value="{{ $value }}" {{ $order->publication_permission === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn-order-response">注文対応</button>
                                            <button class="btn-edit-order" onclick="toggleEditMode('{{ $order->id }}', true)">編集</button>
                                            <button class="btn-save-order" onclick="saveOrderInfo('{{ $order->id }}')" style="display: none;">保存</button>
                                            <button class="btn-cancel-order" onclick="toggleEditMode('{{ $order->id }}', false)" style="display: none;">キャンセル</button>
                                        </div>
                                    </td>
                                    
                                    <!-- ファイル管理 -->
                                    <td class="file-management-cell">
                                        <div class="file-section">
                                            @php
                                                $pdfFiles = $order->getPdfFiles();
                                                $hasPdf = count($pdfFiles) > 0;
                                            @endphp
                                            
                                            @if($hasPdf)
                                                <div class="quote-buttons">
                                                    <button onclick="viewQuotePdfDirect('{{ $order->order_id }}')" class="btn-file-select btn-quote-pdf">
                                                        見積り
                                                    </button>
                                                </div>
                                                <div style="font-size: 8px; text-align: center; color: #28a745; margin-top: 2px;">
                                                    ファイルあり ({{ count($pdfFiles) }}件)
                                                </div>
                                            @else
                                                <div class="quote-buttons">
                                                    <button onclick="uploadQuotePdf('{{ $order->order_id }}')" class="btn-file-select btn-quote-upload">
                                                        見積り追加
                                                    </button>
                                                </div>
                                                <div style="font-size: 8px; text-align: center; color: #dc3545; margin-top: 2px;">
                                                    なし
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="file-section">
                                            <div style="font-size: 10px; color: #6f42c1; margin-bottom: 4px; font-weight: bold;">画像</div>
                                            @if($order->hasImages())
                                                <button onclick="viewOrderImages('{{ $order->order_id }}')" class="btn-file-select btn-image-view">
                                                    画像を表示
                                                </button>
                                            @else
                                                <button onclick="selectImageFiles('{{ $order->order_id }}')" class="btn-file-select btn-file-select-disabled">
                                                    ファイル選択
                                                </button>
                                            @endif
                                            <div style="font-size: 8px; text-align: center; color: #6f42c1;">
                                                @if($order->hasImages())
                                                    ✅ あり
                                                @else
                                                    ⚠️ なし
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- 注文対応フィールド -->
                                    <td class="process-info auto-save-field" data-field="order_handler_id" data-order-id="{{ $order->id }}">
                                        <select class="process-select person-select" data-field="order_handler_id" onchange="autoSaveField(this)">
                                            <option value="">選択してください</option>
                                            @foreach(\App\Models\OrderHandler::active()->get() as $handler)
                                                <option value="{{ $handler->id }}" {{ $order->order_handler_id == $handler->id ? 'selected' : '' }}>
                                                    {{ $handler->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="image_sent_date" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="image_sent_date" value="{{ $order->image_sent_date ? $order->image_sent_date->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="payment_method_id" data-order-id="{{ $order->id }}">
                                        <select class="process-select payment-select" data-field="payment_method_id" onchange="autoSaveField(this)">
                                            <option value="">選択してください</option>
                                            @foreach(\App\Models\PaymentMethod::active()->get() as $method)
                                                <option value="{{ $method->id }}" {{ $order->payment_method_id == $method->id ? 'selected' : '' }}>
                                                    {{ $method->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="payment_completed_date" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="payment_completed_date" value="{{ $order->payment_completed_date ? $order->payment_completed_date->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <!-- プリント工程フィールド -->
                                    <td class="process-info auto-save-field" data-field="print_request_date" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="print_request_date" value="{{ $order->print_request_date ? $order->print_request_date->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="print_factory_id" data-order-id="{{ $order->id }}">
                                        <select class="process-select factory-select" data-field="print_factory_id" onchange="autoSaveField(this)">
                                            <option value="">選択してください</option>
                                            @foreach(\App\Models\PrintFactory::active()->get() as $factory)
                                                <option value="{{ $factory->id }}" {{ $order->print_factory_id == $factory->id ? 'selected' : '' }}>
                                                    {{ $factory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="print_deadline" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="print_deadline" value="{{ $order->print_deadline ? $order->print_deadline->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <!-- 縫製工程フィールド -->
                                    <td class="process-info auto-save-field" data-field="sewing_request_date" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="sewing_request_date" value="{{ $order->sewing_request_date ? $order->sewing_request_date->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="sewing_factory_id" data-order-id="{{ $order->id }}">
                                        <select class="process-select factory-select" data-field="sewing_factory_id" onchange="autoSaveField(this)">
                                            <option value="">選択してください</option>
                                            @foreach(\App\Models\SewingFactory::active()->get() as $factory)
                                                <option value="{{ $factory->id }}" {{ $order->sewing_factory_id == $factory->id ? 'selected' : '' }}>
                                                    {{ $factory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="sewing_deadline" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="sewing_deadline" value="{{ $order->sewing_deadline ? $order->sewing_deadline->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <!-- 検品・発送フィールド -->
                                    <td class="process-info auto-save-field" data-field="quality_check_date" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="quality_check_date" value="{{ $order->quality_check_date ? $order->quality_check_date->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <td class="process-info auto-save-field" data-field="shipping_date" data-order-id="{{ $order->id }}">
                                        <input type="date" class="process-input date-input" data-field="shipping_date" value="{{ $order->shipping_date ? $order->shipping_date->format('Y-m-d') : '' }}" onchange="autoSaveField(this)">
                                    </td>
                                    
                                    <!-- 備考フィールド -->
                                    <td class="editable-cell remarks" data-field="notes">
                                        <span class="notes-display">{{ $order->notes ?? $order->remarks_from_content ?? '-' }}</span>
                                        <textarea class="editable-field notes-textarea" style="display: none; width: 100%; height: 60px; resize: vertical;">{{ $order->notes ?? $order->remarks_from_content ?? '' }}</textarea>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="loading">
                                        データがありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- フッターページネーション -->
                @if($orders->hasPages())
                    <div class="footer-pagination-container">
                        <div class="footer-pagination-info">
                            <span style="font-size: 13px; color: #495057;">
                                ページ {{ $orders->currentPage() }} / {{ $orders->lastPage() }} 
                                （{{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }}件 / {{ $orders->total() }}件）
                            </span>
                        </div>
                        
                        <div class="custom-pagination">
                            @if($orders->onFirstPage())
                                <span class="page-btn disabled">‹‹</span>
                                <span class="page-btn disabled">‹</span>
                            @else
                                <a href="{{ $orders->url(1) }}" class="page-btn">‹‹</a>
                                <a href="{{ $orders->previousPageUrl() }}" class="page-btn">‹</a>
                            @endif
                            
                            @php
                                $current = $orders->currentPage();
                                $last = $orders->lastPage();
                                $start = max(1, $current - 2);
                                $end = min($last, $current + 2);
                            @endphp
                            
                            @if($start > 1)
                                <a href="{{ $orders->url(1) }}" class="page-btn">1</a>
                                @if($start > 2)
                                    <span class="page-btn disabled">...</span>
                                @endif
                            @endif
                            
                            @for($page = $start; $page <= $end; $page++)
                                @if($page == $current)
                                    <span class="page-btn current">{{ $page }}</span>
                                @else
                                    <a href="{{ $orders->url($page) }}" class="page-btn">{{ $page }}</a>
                                @endif
                            @endfor
                            
                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="page-btn disabled">...</span>
                                @endif
                                <a href="{{ $orders->url($last) }}" class="page-btn">{{ $last }}</a>
                            @endif
                            
                            @if($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}" class="page-btn">›</a>
                                <a href="{{ $orders->url($orders->lastPage()) }}" class="page-btn">››</a>
                            @else
                                <span class="page-btn disabled">›</span>
                                <span class="page-btn disabled">››</span>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <!-- 簡易表示は省略 -->
                <div class="loading">簡易表示は開発中です。詳細表示をご利用ください。</div>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    


    <script>
        // CSRF設定
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 編集モード管理用のストレージ
        let editModeData = {};
        
        // 編集モード状態管理
        function toggleEditMode(orderId, isEdit) {
            const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
            if (!orderRow) {
                console.error('Order row not found for ID:', orderId);
                return;
            }
            
            if (isEdit) {
                startEditMode(orderRow, orderId);
            } else {
                cancelEditMode(orderRow, orderId);
            }
        }

        function startEditMode(orderRow, orderId) {
            // 編集可能フィールドのリスト
            const editableFields = [
                'category', 'customer-name', 'company-name', 'delivery-date', 'publication-permission',
                'order-handler', 'image-sent', 'payment-method', 'payment-completed',
                'print-request', 'print-factory', 'print-deadline',
                'sewing-request', 'sewing-factory', 'sewing-deadline',
                'quality-check', 'shipping-date', 'notes'
            ];
            
            editableFields.forEach(field => {
                const displayElement = orderRow.querySelector(`.${field}-display`);
                const inputElement = orderRow.querySelector(`.${field}-input, .${field}-select, .${field}-textarea, .${field}-dropdown`);
                
                if (displayElement && inputElement) {
                    // 編集前の値を保存（表示要素から現在の値を正確に取得）
                    if (!editModeData[orderId]) {
                        editModeData[orderId] = {};
                    }
                    
                    // 表示要素から現在の値を取得（これが最新の保存済み値）
                    let currentValue = displayElement.textContent.trim();
                    if (currentValue === '-') {
                        currentValue = '';
                    }
                    
                    console.log(`startEditMode - Field: ${field}, Current display value: "${currentValue}", Input element:`, inputElement); // デバッグログ
                    
                    // editModeDataに表示値を保存
                    editModeData[orderId][field] = currentValue;
                    
                    // 入力要素の値を表示値に同期（重要：これでキャッシュ問題を解決）
                    if (inputElement.tagName === 'SELECT') {
                        // セレクトボックスの場合：表示テキストに対応するvalueを設定
                        for (let option of inputElement.options) {
                            if (option.text === currentValue || option.value === currentValue) {
                                inputElement.value = option.value;
                                break;
                            }
                        }
                    } else if (inputElement.tagName === 'TEXTAREA') {
                        // テキストエリアの場合：表示値を設定
                        inputElement.value = currentValue;
                    } else {
                        // 入力フィールドの場合：表示値を設定
                        inputElement.value = currentValue;
                    }
                    
                    console.log(`startEditMode - Field: ${field}, Input value set to: "${inputElement.value}"`); // デバッグログ
                    
                    // 表示を切り替え
                    displayElement.style.display = 'none';
                    inputElement.style.display = 'block';
                }
            });
            
            // ボタンの表示を切り替え
            const editButton = orderRow.querySelector('.btn-edit-order');
            const saveButton = orderRow.querySelector('.btn-save-order');
            const cancelButton = orderRow.querySelector('.btn-cancel-order');
            
            if (editButton) editButton.style.display = 'none';
            if (saveButton) saveButton.style.display = 'inline-block';
            if (cancelButton) cancelButton.style.display = 'inline-block';
        }

        function cancelEditMode(orderRow, orderId) {
            const editableFields = [
                'category', 'customer-name', 'company-name', 'delivery-date', 'publication-permission',
                'order-handler', 'image-sent', 'payment-method', 'payment-completed',
                'print-request', 'print-factory', 'print-deadline',
                'sewing-request', 'sewing-factory', 'sewing-deadline',
                'quality-check', 'shipping-date', 'notes'
            ];
            
            editableFields.forEach(field => {
                const displayElement = orderRow.querySelector(`.${field}-display`);
                const inputElement = orderRow.querySelector(`.${field}-input, .${field}-select, .${field}-textarea, .${field}-dropdown`);
                
                if (displayElement && inputElement && editModeData[orderId] && editModeData[orderId][field] !== undefined) {
                    // 元の値に戻す（editModeDataに保存された表示値）
                    const originalValue = editModeData[orderId][field];
                    
                    if (inputElement.tagName === 'SELECT') {
                        // セレクトボックス：表示テキストに対応するvalueを設定
                        for (let option of inputElement.options) {
                            if (option.text === originalValue || option.value === originalValue) {
                                inputElement.value = option.value;
                                break;
                            }
                        }
                    } else if (inputElement.tagName === 'TEXTAREA') {
                        inputElement.value = originalValue;
                    } else {
                        inputElement.value = originalValue;
                    }
                    
                    // 表示を切り替え
                    displayElement.style.display = 'block';
                    inputElement.style.display = 'none';
                }
            });
            
            // ボタンの表示を切り替え
            const editButton = orderRow.querySelector('.btn-edit-order');
            const saveButton = orderRow.querySelector('.btn-save-order');
            const cancelButton = orderRow.querySelector('.btn-cancel-order');
            
            if (editButton) editButton.style.display = 'inline-block';
            if (saveButton) {
                saveButton.style.display = 'none';
                saveButton.textContent = '保存';
                saveButton.disabled = false;
            }
            if (cancelButton) cancelButton.style.display = 'none';
            
            // 編集データをクリア
            delete editModeData[orderId];
        }

        function saveOrderInfo(orderId) {
            const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
            if (!orderRow) {
                console.error('Order row not found for ID:', orderId);
                return;
            }
            
            // 保存中表示
            const saveButton = orderRow.querySelector('.btn-save-order');
            if (saveButton) {
                saveButton.textContent = '保存中...';
                saveButton.disabled = true;
            }
            
            // 全フィールドのデータを収集
            const saveData = {};
            
            // 各フィールドのデータを取得
            const fields = {
                'category': '.category-dropdown',
                'customer_name': '.customer-name-input',
                'company_name': '.company-name-input',
                'delivery_date': '.delivery-date-input',
                'publication_permission': '.publication-permission-select',
                'order_handler_id': '.order-handler-select',
                'image_sent_date': '.image-sent-input',
                'payment_method_id': '.payment-method-select',
                'payment_completed_date': '.payment-completed-input',
                'print_request_date': '.print-request-input',
                'print_factory_id': '.print-factory-select',
                'print_deadline': '.print-deadline-input',
                'sewing_request_date': '.sewing-request-input',
                'sewing_factory_id': '.sewing-factory-select',
                'sewing_deadline': '.sewing-deadline-input',
                'quality_check_date': '.quality-check-input',
                'shipping_date': '.shipping-date-input',
                'notes': '.notes-textarea'
            };
            
            for (const [field, selector] of Object.entries(fields)) {
                const element = orderRow.querySelector(selector);
                if (element && element.style.display !== 'none') {
                    saveData[field] = element.value;
                }
            }
            
            // 複数フィールドの一括保存
            const savePromises = [];
            
            for (const [field, value] of Object.entries(saveData)) {
                if (value !== undefined && value !== null) {
                    savePromises.push(saveOrderField(orderId, field, value));
                }
            }
            
            Promise.all(savePromises)
                .then(results => {
                    // 成功時の処理
                    alert('注文情報を更新しました');
                    
                    // 表示値を更新（保存された値を使用）
                    results.forEach(result => {
                        if (result.success) {
                            updateFieldDisplay(orderRow, result.field, result.value, result.responseData);
                            // 入力フィールドの値も更新（次回編集時のため）
                            updateInputFieldValue(orderRow, result.field, result.value);
                        }
                    });
                    
                    // 更新日時を更新
                    if (results.length > 0 && results[0].responseData && results[0].responseData.updated_at) {
                        const updatedAtElement = orderRow.querySelector('.order-date:last-child');
                        if (updatedAtElement) {
                            updatedAtElement.textContent = `更新日時：${results[0].responseData.updated_at}`;
                        }
                    }
                    
                    // 保存ボタンの状態をリセット
                    if (saveButton) {
                        saveButton.textContent = '保存';
                        saveButton.disabled = false;
                    }
                    
                    // 編集モード終了
                    cancelEditMode(orderRow, orderId);
                    
                    // 編集データをクリア
                    delete editModeData[orderId];
                })
                .catch(error => {
                    console.error('保存エラー:', error);
                    alert('保存に失敗しました: ' + error.message);
                    
                    // 保存ボタンを復元
                    if (saveButton) {
                        saveButton.textContent = '保存';
                        saveButton.disabled = false;
                    }
                });
        }

        function updateFieldDisplay(orderRow, field, value, responseData) {
            // フィールドに応じた表示更新
            if (field === 'order_handler_id') {
                const displayElement = orderRow.querySelector('.order-handler-display');
                if (displayElement && responseData.order_handler_name) {
                    displayElement.textContent = responseData.order_handler_name;
                }
            } else if (field === 'payment_method_id') {
                const displayElement = orderRow.querySelector('.payment-method-display');
                if (displayElement && responseData.payment_method_name) {
                    displayElement.textContent = responseData.payment_method_name;
                }
            } else if (field === 'print_factory_id') {
                const displayElement = orderRow.querySelector('.print-factory-display');
                if (displayElement && responseData.print_factory_name) {
                    displayElement.textContent = responseData.print_factory_name;
                }
            } else if (field === 'sewing_factory_id') {
                const displayElement = orderRow.querySelector('.sewing-factory-display');
                if (displayElement && responseData.sewing_factory_name) {
                    displayElement.textContent = responseData.sewing_factory_name;
                }
            } else if (field === 'category') {
                // カテゴリフィールドの表示更新
                const displayElement = orderRow.querySelector('.category-display');
                if (displayElement) {
                    displayElement.textContent = value || '-';
                    // カテゴリの色も更新
                    displayElement.className = `category-display category-${value.toLowerCase().replace(' ', '')}`;
                }
            } else if (field === 'customer_name') {
                // 注文者名フィールドの表示更新
                const displayElement = orderRow.querySelector('.customer-name-display');
                if (displayElement) {
                    displayElement.textContent = value || '-';
                }
            } else if (field === 'company_name') {
                // 会社名フィールドの表示更新
                const displayElement = orderRow.querySelector('.company-name-display');
                if (displayElement) {
                    displayElement.textContent = value || '-';
                }
            } else if (field === 'delivery_date') {
                // 納期フィールドの表示更新
                const displayElement = orderRow.querySelector('.delivery-date-display');
                if (displayElement) {
                    displayElement.textContent = value || '-';
                }
            } else if (field === 'publication_permission') {
                // 制作事例フィールドの表示更新
                const displayElement = orderRow.querySelector('.publication-permission-display');
                if (displayElement) {
                    displayElement.textContent = value || '-';
                }
            } else if (field.includes('_date')) {
                // 日付フィールドの表示更新
                const displayElement = orderRow.querySelector(`.${field.replace('_', '-')}-display`);
                if (displayElement) {
                    if (value) {
                        const date = new Date(value);
                        displayElement.textContent = `${date.getMonth() + 1}/${date.getDate()}`;
                    } else {
                        displayElement.textContent = '-';
                    }
                }
            } else {
                // その他のフィールド
                const displayElement = orderRow.querySelector(`.${field.replace('_', '-')}-display`);
                if (displayElement) {
                    displayElement.textContent = value || '-';
                }
            }
        }

        // 個別フィールド保存
        function saveOrderField(orderId, field, value) {
            return new Promise((resolve, reject) => {
                const data = {};
                data[field] = value;
                
                fetch(`/orders/${orderId}/update-info`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resolve({ 
                            success: true, 
                            field: field, 
                            value: value,
                            responseData: data.data || {} 
                        });
                    } else {
                        reject(new Error(data.message || `${field} の更新に失敗しました`));
                    }
                })
                .catch(error => {
                    reject(error);
                });
            });
        }

        // 入力フィールドの値を更新（保存後のキャッシュ問題解決）
        function updateInputFieldValue(orderRow, field, value) {
            console.log('updateInputFieldValue called:', field, value); // デバッグログ
            
            const inputElement = orderRow.querySelector(`.${field.replace('_', '-')}-input, .${field.replace('_', '-')}-select, .${field.replace('_', '-')}-textarea, .${field.replace('_', '-')}-dropdown`);
            
            console.log('Found input element:', inputElement); // デバッグログ
            
            if (inputElement) {
                console.log('Current input value before update:', inputElement.value); // デバッグログ
                
                if (inputElement.tagName === 'SELECT') {
                    inputElement.value = value;
                } else if (inputElement.tagName === 'TEXTAREA') {
                    inputElement.value = value;
                } else {
                    inputElement.value = value;
                }
                
                console.log('Input value after update:', inputElement.value); // デバッグログ
                
                // data-original-value属性も更新（カテゴリの場合）
                if (field === 'category' && inputElement.hasAttribute('data-original-value')) {
                    inputElement.setAttribute('data-original-value', value);
                }
            } else {
                console.log('Input element not found for field:', field); // デバッグログ
            }
        }

        // 商品カテゴリ更新
        function updateProductCategory(orderId, category) {
            // 編集モードでない場合は操作を無効化
            if (!editModeData[orderId]) {
                // 編集モードでない場合は元の値に戻す
                const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
                if (orderRow) {
                    const categorySelect = orderRow.querySelector('.category-dropdown');
                    if (categorySelect) {
                        // 元の値に戻す（data属性から取得）
                        const originalValue = categorySelect.getAttribute('data-original-value');
                        if (originalValue) {
                            categorySelect.value = originalValue;
                        }
                    }
                }
                alert('カテゴリを変更するには編集モードに入ってください');
                return;
            }
            
            // 編集モードの場合のみ変更を許可
            console.log('カテゴリ更新:', orderId, category);
        }

        // タブ変更
        function changeTab(tab) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.location.href = url.toString();
        }

        // ファイル関連機能
        function viewQuotePdfDirect(orderId) {
            // 注文IDから数値部分を抽出（#1310 → 1310）
            const numericId = orderId.replace('#', '');
            // 5桁のゼロパディング（1310 → 01310）
            const paddedId = numericId.padStart(5, '0');
            // PDFビューワーURLを生成（相対パス使用）
            const viewerUrl = `/pdf/${paddedId}/viewer`;
            
            // モーダルウィンドウでPDFを表示
            showPdfModal(viewerUrl, orderId);
        }
        
        // PDF.jsライブラリの動的読み込み
        function loadPdfJsLibrary() {
            if (window.pdfjsLib) {
                return Promise.resolve(); // 既に読み込み済み
            }
            
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                // Cloudflare CDNを使用（最も安定）
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js';
                script.onload = function() {
                    window.pdfjsLib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
                    // ワーカーもCloudflare CDNから読み込み
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';
                    console.log('PDF.js library loaded successfully from Cloudflare CDN');
                    resolve();
                };
                script.onerror = function() {
                    console.error('PDF.js library failed to load from Cloudflare CDN');
                    // フォールバック1: JSDelivrを試す
                    const fallbackScript1 = document.createElement('script');
                    fallbackScript1.src = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.6.347/build/pdf.min.js';
                    fallbackScript1.onload = function() {
                        window.pdfjsLib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
                        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.6.347/build/pdf.worker.min.js';
                        console.log('PDF.js library loaded successfully from JSDelivr CDN (fallback 1)');
                        resolve();
                    };
                    fallbackScript1.onerror = function() {
                        console.error('PDF.js library failed to load from JSDelivr CDN');
                        // フォールバック2: 古いバージョンのCloudflareを試す
                        const fallbackScript2 = document.createElement('script');
                        fallbackScript2.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.min.js';
                        fallbackScript2.onload = function() {
                            window.pdfjsLib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
                            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.worker.min.js';
                            console.log('PDF.js library loaded successfully from Cloudflare CDN (fallback 2 - older version)');
                            resolve();
                        };
                        fallbackScript2.onerror = function() {
                            console.error('PDF.js library failed to load from all CDN sources');
                            reject(new Error('PDF.js library failed to load from all CDN sources. Please check your internet connection.'));
                        };
                        document.head.appendChild(fallbackScript2);
                    };
                    document.head.appendChild(fallbackScript1);
                };
                document.head.appendChild(script);
            });
        }

        function showPdfModal(pdfUrl, orderId) {
            // 注文IDから数値部分を抽出
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const pdfFileUrl = `/pdf/${paddedId}`;
            
            // 新しいPDFモーダル設計：黒透過背景 + iframe表示 + 管理機能
            const modalHtml = `
                <div id="pdfModal" style="
                    display: block;
                    position: fixed;
                    z-index: 10000;
                    left: 0;
                    top: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.8);
                    backdrop-filter: blur(5px);
                    overflow: hidden;
                ">
                    <!-- PDFモーダルコンテンツ -->
                    <div style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 90vw;
                        height: 90vh;
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
                        display: flex;
                        flex-direction: column;
                        overflow: hidden;
                    ">
                        <!-- ヘッダー -->
                        <div style="
                            background: linear-gradient(135deg, #2c3e50, #34495e);
                            color: white;
                            padding: 15px 20px;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-radius: 12px 12px 0 0;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                        ">
                            <div>
                                <h3 style="margin: 0; font-size: 18px; font-weight: 600;">見積りPDF - 注文 ${orderId}</h3>
                                <p style="margin: 5px 0 0 0; font-size: 12px; opacity: 0.8;">PDF管理システム</p>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <!-- PDF管理ボタン -->
                                <button onclick="addPdfToModal('${orderId}')" style="
                                    background: #27ae60;
                                    color: white;
                                    border: none;
                                    padding: 8px 16px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-size: 12px;
                                    display: flex;
                                    align-items: center;
                                    gap: 5px;
                                    transition: all 0.2s;
                                " onmouseover="this.style.background='#219a52'" onmouseout="this.style.background='#27ae60'">
                                    <span>➕</span> PDF追加
                                </button>

                                <button onclick="closePdfModal()" style="
                                    background: #95a5a6;
                                    color: white;
                                    border: none;
                                    padding: 8px 12px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-size: 14px;
                                    transition: all 0.2s;
                                " onmouseover="this.style.background='#7f8c8d'" onmouseout="this.style.background='#95a5a6'">
                                    ✕
                                </button>
                            </div>
                        </div>
                        
                        <!-- PDFコンテンツエリア -->
                        <div style="
                            flex: 1;
                            display: flex;
                            position: relative;
                            overflow: hidden;
                        ">
                            <!-- PDF表示エリア -->
                            <div id="pdfViewerArea" style="
                                flex: 1;
                                background: #f8f9fa;
                                position: relative;
                                overflow: hidden;
                            ">
                                <!-- ローディング表示 -->
                                <div id="pdfLoading" style="
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    text-align: center;
                                    color: #6c757d;
                                ">
                                    <div style="
                                        width: 50px;
                                        height: 50px;
                                        border: 4px solid #e9ecef;
                                        border-top: 4px solid #3498db;
                                        border-radius: 50%;
                                        animation: spin 1s linear infinite;
                                        margin: 0 auto 15px;
                                    "></div>
                                    <p style="margin: 0; font-size: 14px;">PDFを読み込み中...</p>
                                </div>
                                
                                <!-- PDF iframe -->
                                <iframe id="pdfIframe" style="
                                    width: 100%;
                                    height: 100%;
                                    border: none;
                                    display: none;
                                " src="${pdfFileUrl}">
                                </iframe>
                                
                                <!-- エラー表示 -->
                                <div id="pdfErrorMessage" style="
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    text-align: center;
                                    color: #e74c3c;
                                    display: none;
                                ">
                                    <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                                    <h4 style="margin: 0 0 10px 0; color: #2c3e50;">PDFの読み込みに失敗しました</h4>
                                    <p style="margin: 0; color: #6c757d;">ファイルが見つからないか、破損している可能性があります。</p>
                                </div>
                            </div>
                            
                            <!-- ページ管理サイドバー（初期非表示） -->
                            <div id="pdfPagesSidebar" style="
                                width: 300px;
                                background: #ffffff;
                                border-left: 1px solid #dee2e6;
                                display: flex;
                                flex-direction: column;
                                overflow-y: auto;
                            ">
                                <div style="
                                    padding: 15px;
                                    border-bottom: 1px solid #dee2e6;
                                    background: #f8f9fa;
                                ">
                                    <h4 style="margin: 0; font-size: 14px; color: #2c3e50;">ページ管理</h4>
                                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #6c757d;">PDFページの順序を変更できます</p>
                                </div>
                                <div id="pdfPagesList" style="
                                    padding: 15px;
                                    flex: 1;
                                ">
                                    <!-- ページ一覧がここに動的に生成される -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- フッター -->
                        <div style="
                            background: #f8f9fa;
                            padding: 12px 20px;
                            border-top: 1px solid #dee2e6;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-radius: 0 0 12px 12px;
                        ">
                            <div style="font-size: 12px; color: #6c757d;">
                                <span id="pdfInfo">PDF情報を取得中...</span>
                            </div>
                            <div style="font-size: 12px; color: #6c757d;">
                                ESCキーまたは背景クリックで閉じる
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- スピンアニメーション -->
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // ページのスクロールを無効化
            document.body.style.overflow = 'hidden';

            // PDFを読み込み
            loadPdfInModal(pdfFileUrl, orderId);

            // PDFページリストを自動読み込み
            loadPdfPagesList(orderId);

            // ESCキーで閉じる
            document.addEventListener('keydown', handleEscKey);
            
            // 背景クリックで閉じる
            document.getElementById('pdfModal').addEventListener('click', function(e) {
                if (e.target.id === 'pdfModal') {
                    closePdfModal();
                }
            });
        }

        function loadPdfInModal(pdfUrl, orderId) {
            const iframe = document.getElementById('pdfIframe');
            const loading = document.getElementById('pdfLoading');
            const errorMsg = document.getElementById('pdfErrorMessage');
            const pdfInfo = document.getElementById('pdfInfo');
            
            // ローディング表示
            loading.style.display = 'block';
            iframe.style.display = 'none';
            errorMsg.style.display = 'none';
            
            // iframe読み込み完了イベント
            iframe.onload = function() {
                loading.style.display = 'none';
                iframe.style.display = 'block';
                pdfInfo.textContent = `PDF表示中 - 注文 ${orderId}`;
                console.log('PDF読み込み完了:', pdfUrl);
            };
            
            // iframe読み込みエラーイベント
            iframe.onerror = function() {
                loading.style.display = 'none';
                errorMsg.style.display = 'block';
                pdfInfo.textContent = 'PDF読み込みエラー';
                console.error('PDF読み込みエラー:', pdfUrl);
            };
            
            // PDFを読み込み
            iframe.src = pdfUrl;
        }

        function addPdfToModal(orderId) {
            // ファイル選択ダイアログを作成
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf';
            input.multiple = true; // 複数ファイル選択対応
            
            input.onchange = function(event) {
                const files = Array.from(event.target.files);
                if (files.length > 0) {
                    files.forEach(file => {
                        if (file.type !== 'application/pdf') {
                            showToast(`${file.name} はPDFファイルではありません`, 'error');
                            return;
                        }
                        
                        if (file.size > 10 * 1024 * 1024) { // 10MB制限
                            showToast(`${file.name} のファイルサイズが大きすぎます（10MB以下）`, 'error');
                            return;
                        }
                        
                        uploadPdfFileToModal(orderId, file);
                    });
                }
            };
            
            input.click();
        }

        function uploadPdfFileToModal(orderId, file) {
            const formData = new FormData();
            formData.append('pdf_file', file);
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            showToast(`${file.name} をアップロード中...`, 'info');
            
            fetch(`/pdf/${paddedId}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(`${file.name} がアップロードされました`, 'success');
                    // アップロード後、最新のPDFファイルを表示
                    refreshPdfInModal(orderId);
                    // PDF情報を更新
                    updatePdfInfo(orderId, data.files);
                    // PDFページリストを更新
                    loadPdfPagesList(orderId);
                } else {
                    showToast(`アップロードに失敗しました: ${data.message || '不明なエラー'}`, 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showToast('アップロードに失敗しました', 'error');
            });
        }

        function deletePdfFromModal(orderId) {
            if (!confirm('この注文のPDFファイルを削除しますか？')) {
                return;
            }
            
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const filename = `${paddedId}.pdf`;
            
            fetch(`/pdf/${orderId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    filename: filename
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // 削除後の状態を更新
                    if (data.count === 0) {
                        // 全てのPDFが削除された場合
                        showNoPdfMessage();
                    } else {
                        // まだPDFが残っている場合は最新を表示
                        refreshPdfInModal(orderId);
                    }
                    // PDF情報を更新
                    updatePdfInfo(orderId, data.files);
                } else {
                    showToast(`削除に失敗しました: ${data.message || '不明なエラー'}`, 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('削除に失敗しました', 'error');
            });
        }

        function togglePdfPages(orderId) {
            const sidebar = document.getElementById('pdfPagesSidebar');
            const viewerArea = document.getElementById('pdfViewerArea');
            
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                // サイドバーを表示
                sidebar.style.display = 'flex';
                viewerArea.style.borderRadius = '0';
                loadPdfPagesList(orderId);
            } else {
                // サイドバーを非表示
                sidebar.style.display = 'none';
                viewerArea.style.borderRadius = '0 0 12px 12px';
            }
        }

        function loadPdfPagesList(orderId) {
            const pagesList = document.getElementById('pdfPagesList');
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            // PDF一覧を取得
            fetch(`/pdf/${paddedId}/list`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.files.length > 0) {
                        let html = `
                            <div style="margin-bottom: 15px;">
                                <h5 style="margin: 0; font-size: 14px; color: #2c3e50;">PDFファイル一覧</h5>
                                <p style="margin: 5px 0 0 0; font-size: 12px; color: #6c757d;">${data.files.length}個のファイル</p>
                            </div>
                        `;
                        
                        data.files.forEach((file, index) => {
                            html += `
                                <div class="pdf-page-item" 
                                     data-filename="${file.name}"
                                     data-order="${index}"
                                     style="
                                        background: white;
                                        border: 1px solid #dee2e6;
                                        border-radius: 6px;
                                        padding: 12px;
                                        margin-bottom: 8px;
                                        cursor: grab;
                                        transition: all 0.2s;
                                        position: relative;
                                     " 
                                     onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                     onmouseout="this.style.backgroundColor='white'">
                                    
                                    <!-- ドラッグハンドル -->
                                    <div style="
                                        position: absolute;
                                        left: 5px;
                                        top: 50%;
                                        transform: translateY(-50%);
                                        color: #bdc3c7;
                                        font-size: 12px;
                                        cursor: grab;
                                    ">⋮⋮</div>
                                    
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-left: 20px;">
                                        <div style="flex: 1; cursor: pointer;" onclick="viewPdfFile('${orderId}', '${file.name}')">
                                            <div style="font-size: 13px; font-weight: 500; color: #2c3e50; margin-bottom: 4px;">
                                                ${file.name}
                                            </div>
                                            <div style="font-size: 11px; color: #6c757d;">
                                                ${file.type === 'main' ? 'メインPDF' : '追加PDF'} • クリックで表示
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 5px;">
                                            <button onclick="event.stopPropagation(); deletePdfFileFromModal('${orderId}', '${file.name}')" style="
                                                background: #e74c3c;
                                                color: white;
                                                border: none;
                                                padding: 4px 8px;
                                                border-radius: 4px;
                                                cursor: pointer;
                                                font-size: 10px;
                                            ">削除</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        pagesList.innerHTML = html;
                        
                        // SortableJS初期化
                        if (window.Sortable) {
                            // 既存のSortableインスタンスを安全に削除
                            if (window.currentSortable && typeof window.currentSortable.destroy === 'function') {
                                try {
                                    window.currentSortable.destroy();
                                    window.currentSortable = null;
                                } catch (e) {
                                    console.warn('SortableJS destroy error:', e);
                                    window.currentSortable = null;
                                }
                            }
                            
                            let isReordering = false; // 重複実行防止フラグ
                            
                            try {
                                window.currentSortable = Sortable.create(pagesList, {
                                    animation: 150,
                                    ghostClass: 'sortable-ghost',
                                    chosenClass: 'sortable-chosen',
                                    dragClass: 'sortable-drag',
                                    handle: '.pdf-page-item',
                                    disabled: false,
                                    onEnd: function(evt) {
                                        // 重複実行防止
                                        if (isReordering) {
                                            console.log('Reorder already in progress, skipping...');
                                            return;
                                        }
                                        
                                        // 同じ位置にドロップされた場合は何もしない
                                        if (evt.oldIndex === evt.newIndex) {
                                            return;
                                        }
                                        
                                        isReordering = true;
                                        
                                        // スクロール位置を保存
                                        const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
                                        
                                        const items = Array.from(pagesList.children);
                                        const draggedItem = items[evt.newIndex];
                                        const targetItem = items[evt.oldIndex];
                                        
                                        if (draggedItem && targetItem) {
                                            const draggedFilename = draggedItem.getAttribute('data-filename');
                                            const targetFilename = targetItem.getAttribute('data-filename');
                                            
                                            if (draggedFilename && targetFilename && draggedFilename !== targetFilename) {
                                                const reorderData = {
                                                    pages: [
                                                        { filename: draggedFilename, order: evt.oldIndex + 1 },
                                                        { filename: targetFilename, order: evt.newIndex + 1 }
                                                    ]
                                                };
                                                
                                                console.log('SortableJS reorder:', reorderData);
                                                
                                                const numericId = orderId.replace('#', '');
                                                const paddedId = numericId.padStart(5, '0');
                                                
                                                fetch(`/pdf/${paddedId}/reorder`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                    },
                                                    body: JSON.stringify(reorderData)
                                                })
                                                .then(response => {
                                                    console.log('Reorder response status:', response.status);
                                                    console.log('Reorder response headers:', response.headers);
                                                    
                                                    if (!response.ok) {
                                                        return response.text().then(text => {
                                                            console.error('Reorder error response:', text);
                                                            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                                                        });
                                                    }
                                                    return response.json();
                                                })
                                                .then(data => {
                                                    console.log('Reorder success response:', data);
                                                    if (data.success) {
                                                        showToast('PDFページの順序が変更されました', 'success');
                                                        // 成功時は再読み込みしない（UIは既に更新済み）
                                                        // スクロール位置を復元
                                                        window.scrollTo(0, currentScrollTop);
                                                    } else {
                                                        showToast(`順序変更に失敗しました: ${data.message || '不明なエラー'}`, 'error');
                                                        // 失敗した場合のみ元の順序に戻す
                                                        loadPdfPagesList(orderId);
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('SortableJS reorder error:', error);
                                                    showToast('順序変更に失敗しました', 'error');
                                                    // エラーの場合のみ元の順序に戻す
                                                    loadPdfPagesList(orderId);
                                                })
                                                .finally(() => {
                                                    isReordering = false;
                                                });
                                            } else {
                                                isReordering = false;
                                            }
                                        } else {
                                            isReordering = false;
                                        }
                                    }
                                });
                            } catch (error) {
                                console.error('SortableJS initialization error:', error);
                                showToast('ドラッグ&ドロップ機能の初期化に失敗しました', 'error');
                            }
                        }
                    } else {
                        pagesList.innerHTML = `
                            <div style="text-align: center; color: #6c757d; padding: 20px;">
                                <div style="font-size: 32px; margin-bottom: 10px;">📄</div>
                                <p style="margin: 0; font-size: 14px;">PDFファイルがありません</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('PDF list error:', error);
                    pagesList.innerHTML = `
                        <div style="text-align: center; color: #e74c3c; padding: 20px;">
                            <div style="font-size: 32px; margin-bottom: 10px;">⚠️</div>
                            <p style="margin: 0; font-size: 14px;">PDF一覧の取得に失敗しました</p>
                        </div>
                    `;
                });
        }

        function viewPdfFile(orderId, filename) {
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const pdfFileUrl = `/pdf/${paddedId}?file=${encodeURIComponent(filename)}`;
            
            loadPdfInModal(pdfFileUrl, orderId);
            showToast(`${filename} を表示中`, 'info');
        }

        function deletePdfFile(orderId, filename) {
            if (!confirm(`PDFファイル「${filename}」を削除しますか？`)) {
                return;
            }
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            fetch(`/pdf/${paddedId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    filename: filename
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // ページ管理サイドバーを更新
                    loadPdfPagesList(orderId);
                    // メインビューを更新
                    if (data.count === 0) {
                        showNoPdfMessage();
                    } else {
                        refreshPdfInModal(orderId);
                    }
                    // PDF情報を更新
                    updatePdfInfo(orderId, data.files);
                } else {
                    showToast(`削除に失敗しました: ${data.message || '不明なエラー'}`, 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('削除に失敗しました', 'error');
            });
        }

        function refreshPdfInModal(orderId) {
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const pdfFileUrl = `/pdf/${paddedId}`;
            
            loadPdfInModal(pdfFileUrl, orderId);
        }

        function showNoPdfMessage() {
            const iframe = document.getElementById('pdfIframe');
            const loading = document.getElementById('pdfLoading');
            const errorMsg = document.getElementById('pdfErrorMessage');
            
            if (iframe && loading && errorMsg) {
                iframe.style.display = 'none';
                loading.style.display = 'none';
                errorMsg.style.display = 'block';
                errorMsg.innerHTML = `
                    <div style="font-size: 48px; margin-bottom: 15px;">📄</div>
                    <h4 style="margin: 0 0 10px 0; color: #2c3e50;">PDFファイルがありません</h4>
                    <p style="margin: 0; color: #6c757d;">新しいPDFファイルをアップロードしてください。</p>
                `;
            }
        }

        function updatePdfInfo(orderId, files) {
            const pdfInfo = document.getElementById('pdfInfo');
            if (pdfInfo) {
                if (files && files.length > 0) {
                    pdfInfo.textContent = `PDF表示中 - 注文 ${orderId} (${files.length}個のファイル)`;
                } else {
                    pdfInfo.textContent = `PDF表示 - 注文 ${orderId} (ファイルなし)`;
                }
            }
        }

        function closePdfModal() {
            // SortableJS インスタンスのクリーンアップ
            if (window.currentSortable && typeof window.currentSortable.destroy === 'function') {
                try {
                    window.currentSortable.destroy();
                    window.currentSortable = null;
                } catch (e) {
                    console.warn('SortableJS cleanup error:', e);
                    window.currentSortable = null;
                }
            }
            
            const modal = document.getElementById('pdfModal');
            if (modal) {
                modal.remove();
            }
            
            // ページのスクロールを復元
            document.body.style.overflow = 'auto';
            
            // イベントリスナーを削除
            document.removeEventListener('keydown', handleEscKey);
        }

        function handleEscKey(e) {
            if (e.key === 'Escape') {
                closePdfModal();
            }
        }

        function manageQuotePdf(orderId) {
            if (!confirm(`見積りPDFファイルを削除しますか？`)) {
                return;
            }
            
            // 注文IDから数値部分を抽出してファイル名を生成
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const filename = `${paddedId}.pdf`;
            
            fetch(`/pdf/${orderId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    filename: filename
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('見積りPDFファイルが削除されました', 'success');
                    // ページをリロードしてUI更新
                    location.reload();
                } else {
                    showToast('削除に失敗しました: ' + (data.message || '不明なエラー'), 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('削除に失敗しました', 'error');
            });
        }

        function uploadQuotePdf(orderId) {
            // ファイル選択ダイアログを作成
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf';
            input.multiple = false;
            
            input.onchange = function(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.type !== 'application/pdf') {
                        alert('PDFファイルのみアップロード可能です。');
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) { // 10MB制限
                        alert('ファイルサイズは10MB以下にしてください。');
                        return;
                    }
                    
                    uploadPdfFile(orderId, file);
                }
            };
            
            input.click();
        }

        function uploadPdfFile(orderId, file) {
            const formData = new FormData();
            formData.append('pdf_file', file);
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            // アップロード中の表示
            showToast('PDFファイルをアップロード中...', 'info');
            
            fetch(`/pdf/${paddedId}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('PDFファイルがアップロードされました', 'success');
                    // ページをリロードしてUI更新
                    location.reload();
                } else {
                    showToast('アップロードに失敗しました: ' + (data.message || '不明なエラー'), 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showToast('アップロードに失敗しました', 'error');
            });
        }

        function deletePdfFile(orderId, filename) {
            if (!confirm(`PDFファイル「${filename}」を削除しますか？`)) {
                return;
            }
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            fetch(`/pdf/${paddedId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    filename: filename
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('PDFファイルが削除されました', 'success');
                    // ページをリロードしてUI更新
                    location.reload();
                } else {
                    showToast('削除に失敗しました: ' + (data.message || '不明なエラー'), 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('削除に失敗しました', 'error');
            });
        }

        function showToast(message, type = 'info') {
            // 既存のトースト通知システムを使用
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                // トースト通知コンテナが存在しない場合は作成
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                `;
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.cssText = `
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
                color: white;
                padding: 12px 20px;
                border-radius: 4px;
                margin-bottom: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                animation: slideIn 0.3s ease-out;
            `;
            toast.textContent = message;
            
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function viewOrderImages(orderId) {
            window.open(`/images/${orderId}`, '_blank');
        }

        function selectImageFiles(orderId) {
            alert('画像ファイル選択機能は開発中です。');
        }

        function showNewOrderModal() {
            alert('新規注文機能は開発中です。');
        }

        // 表示件数変更
        function changePerPage(perPage) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.delete('page'); // ページをリセット
            window.location.href = url.toString();
        }

        // プロセスフィールド自動保存
        function autoSaveField(element) {
            const field = element.getAttribute('data-field');
            const orderId = element.closest('.auto-save-field').getAttribute('data-order-id');
            const value = element.value;
            const fieldContainer = element.closest('.auto-save-field');
            
            // 保存中表示
            fieldContainer.classList.add('saving');
            
            const data = {};
            data[field] = value;
            
            fetch(`/orders/${orderId}/update-info`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                fieldContainer.classList.remove('saving');
                
                if (data.success) {
                    // 成功時の視覚的フィードバック
                    fieldContainer.style.backgroundColor = '#d4edda';
                    setTimeout(() => {
                        fieldContainer.style.backgroundColor = '';
                    }, 1500);
                } else {
                    // エラー時の処理
                    fieldContainer.style.backgroundColor = '#f8d7da';
                    setTimeout(() => {
                        fieldContainer.style.backgroundColor = '';
                    }, 3000);
                    
                    // エラーメッセージ表示（簡易版）
                    const errorMsg = document.createElement('div');
                    errorMsg.textContent = data.message || '保存に失敗しました';
                    errorMsg.style.cssText = `
                        position: absolute;
                        top: 100%;
                        left: 0;
                        background: #dc3545;
                        color: white;
                        padding: 4px 8px;
                        border-radius: 3px;
                        font-size: 10px;
                        z-index: 1001;
                        white-space: nowrap;
                    `;
                    fieldContainer.appendChild(errorMsg);
                    setTimeout(() => {
                        if (errorMsg.parentNode) {
                            errorMsg.parentNode.removeChild(errorMsg);
                        }
                    }, 3000);
                }
            })
            .catch(error => {
                fieldContainer.classList.remove('saving');
                fieldContainer.style.backgroundColor = '#f8d7da';
                setTimeout(() => {
                    fieldContainer.style.backgroundColor = '';
                }, 3000);
                console.error('保存エラー:', error);
            });
        }

        function uploadPdfFromModal(orderId) {
            // ファイル選択ダイアログを作成
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf';
            input.multiple = false;
            
            input.onchange = function(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.type !== 'application/pdf') {
                        alert('PDFファイルのみアップロード可能です。');
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) { // 10MB制限
                        alert('ファイルサイズは10MB以下にしてください。');
                        return;
                    }
                    
                    uploadPdfFileFromModal(orderId, file);
                }
            };
            
            input.click();
        }
        
        function uploadPdfFileFromModal(orderId, file) {
            const formData = new FormData();
            formData.append('pdf_file', file);
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            // アップロード中の表示
            showToast('PDFファイルをアップロード中...', 'info');
            
            fetch(`/pdf/${paddedId}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('PDFファイルがアップロードされました', 'success');
                    // PDF一覧を更新
                    loadPdfList(orderId);
                    // 新しいPDFを表示
                    viewPdfInModal(orderId, 'uploaded.pdf');
                } else {
                    showToast('アップロードに失敗しました: ' + (data.message || '不明なエラー'), 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showToast('アップロードに失敗しました', 'error');
            });
        }
        
        function deletePdfFromModal(orderId) {
            if (!confirm(`見積りPDFファイルを削除しますか？`)) {
                return;
            }
            
            // 注文IDから数値部分を抽出してファイル名を生成
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const filename = `${paddedId}.pdf`;
            
            fetch(`/pdf/${orderId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    filename: filename
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('見積りPDFファイルが削除されました', 'success');
                    // PDF一覧を更新
                    loadPdfList(orderId);
                    // PDFビューワーをクリア
                    const iframe = document.getElementById('pdfIframe');
                    if (iframe) {
                        iframe.src = '';
                        iframe.style.display = 'none';
                    }
                    // エラーメッセージを表示
                    const errorMsg = document.getElementById('pdfErrorMessage');
                    if (errorMsg) {
                        errorMsg.innerHTML = `
                            <div style="font-size: 24px; margin-bottom: 10px;">📄</div>
                            <div>PDFファイルが削除されました</div>
                        `;
                        errorMsg.style.display = 'block';
                    }
                } else {
                    showToast('削除に失敗しました: ' + (data.message || '不明なエラー'), 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('削除に失敗しました', 'error');
            });
        }

        function loadPdfList(orderId) {
            const pdfListElement = document.getElementById('pdfList');
            if (!pdfListElement) return;
            
            // 注文IDから数値部分を抽出
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            // PDF一覧の表示
            pdfListElement.innerHTML = `
                <div style="margin-bottom: 10px; font-weight: bold; color: #495057;">
                    PDFファイル一覧
                </div>
                <div id="pdfItem_${paddedId}" class="pdf-item" style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 8px 12px;
                    margin-bottom: 5px;
                    background-color: white;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    cursor: pointer;
                ">
                    <div style="
                        display: flex;
                        align-items: center;
                        flex: 1;
                    ">
                        <div style="
                            width: 30px;
                            height: 30px;
                            background-color: #dc3545;
                            color: white;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 4px;
                            margin-right: 10px;
                            font-size: 12px;
                        ">PDF</div>
                        <div>
                            <div style="font-size: 14px; font-weight: 500;">${paddedId}.pdf</div>
                            <div style="font-size: 12px; color: #6c757d;">見積りPDF</div>
                        </div>
                    </div>
                    <div style="
                        display: flex;
                        gap: 5px;
                    ">
                        <button onclick="viewPdfInModal('${orderId}', '${paddedId}.pdf')" style="
                            background-color: #007bff;
                            color: white;
                            border: none;
                            padding: 4px 8px;
                            border-radius: 3px;
                            cursor: pointer;
                            font-size: 11px;
                        ">表示</button>
                    </div>
                </div>
            `;
        }
        
        function viewPdfInModal(orderId, filename) {
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            const pdfUrl = `/pdf/${paddedId}/viewer#view=FitH`;
            
            const iframe = document.getElementById('pdfIframe');
            if (iframe) {
                iframe.src = pdfUrl;
                hidePdfLoading();
            }
        }

        function showPdfError(title, message) {
            const modal = document.getElementById('pdfModal');
            if (modal) {
                modal.innerHTML = `
                    <div style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        background: white;
                        padding: 40px;
                        border-radius: 8px;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                        text-align: center;
                        max-width: 500px;
                        width: 90%;
                    ">
                        <div style="
                            color: #e74c3c;
                            font-size: 48px;
                            margin-bottom: 20px;
                        ">⚠️</div>
                        <h2 style="
                            color: #2c3e50;
                            margin-bottom: 15px;
                            font-size: 24px;
                        ">${title}</h2>
                        <p style="
                            color: #7f8c8d;
                            margin-bottom: 30px;
                            font-size: 16px;
                            line-height: 1.5;
                        ">${message}</p>
                        <div style="
                            margin-bottom: 20px;
                            padding: 15px;
                            background: #f8f9fa;
                            border-radius: 4px;
                            border-left: 4px solid #e74c3c;
                        ">
                            <p style="
                                color: #2c3e50;
                                margin: 0;
                                font-size: 14px;
                            ">
                                <strong>対処方法:</strong><br>
                                • ページを再読み込みしてください<br>
                                • インターネット接続を確認してください<br>
                                • しばらく時間をおいてから再試行してください
                            </p>
                        </div>
                        <button onclick="closePdfModal()" style="
                            background: #3498db;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 16px;
                            margin-right: 10px;
                        ">閉じる</button>
                        <button onclick="location.reload()" style="
                            background: #2ecc71;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 16px;
                        ">ページを再読み込み</button>
                    </div>
                `;
            }
        }

        // 旧ドラッグ&ドロップ機能は削除済み - SortableJSに置換

        function deletePdfFileFromModal(orderId, filename) {
            if (!confirm(`PDFファイル「${filename}」を削除しますか？`)) {
                return;
            }
            
            // 注文IDから数値部分を抽出してパディング
            const numericId = orderId.replace('#', '');
            const paddedId = numericId.padStart(5, '0');
            
            fetch(`/pdf/${paddedId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    filename: filename
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // ページ管理サイドバーを更新（モーダルは閉じない）
                    loadPdfPagesList(orderId);
                    // メインビューを更新
                    if (data.count === 0) {
                        showNoPdfMessage();
                    } else {
                        refreshPdfInModal(orderId);
                    }
                    // PDF情報を更新
                    updatePdfInfo(orderId, data.files);
                } else {
                    showToast(`削除に失敗しました: ${data.message || '不明なエラー'}`, 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('削除に失敗しました', 'error');
            });
        }
    </script>
</body>
</html> 