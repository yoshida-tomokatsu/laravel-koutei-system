@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>PDF管理</h2>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 注文管理に戻る
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">PDF検索</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" id="searchInput" class="form-control" placeholder="注文番号やファイル名で検索...">
                        </div>
                        <div class="col-md-4">
                            <button id="searchBtn" class="btn btn-primary">
                                <i class="fas fa-search"></i> 検索
                            </button>
                            <button id="clearBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> クリア
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">PDFファイル一覧 ({{ count($pdfs) }}件)</h5>
                </div>
                <div class="card-body">
                    <div id="pdfList">
                        @if(count($pdfs) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>フォルダ</th>
                                            <th>注文番号</th>
                                            <th>ファイル名</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pdfs as $pdf)
                                        <tr>
                                            <td>
                                                <span class="badge {{ $pdf['folder'] === '01-000' ? 'bg-primary' : 'bg-info' }}">
                                                    {{ $pdf['folder'] }}
                                                </span>
                                            </td>
                                            <td>{{ $pdf['order_id'] }}</td>
                                            <td>{{ $pdf['filename'] }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pdf.viewer', $pdf['order_id']) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> 表示
                                                    </a>
                                                    <a href="{{ $pdf['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i> 新しいタブ
                                                    </a>
                                                    <a href="{{ $pdf['url'] }}" download class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i> DL
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> PDFファイルが見つかりません。
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearBtn');
    const pdfList = document.getElementById('pdfList');
    
    // 検索実行
    function performSearch() {
        const query = searchInput.value.trim();
        
        if (!query) {
            location.reload();
            return;
        }
        
        // ローディング表示
        pdfList.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">検索中...</p></div>';
        
        // 検索API呼び出し
        fetch(`{{ route('pdf.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('検索エラー:', error);
                pdfList.innerHTML = '<div class="alert alert-danger">検索中にエラーが発生しました。</div>';
            });
    }
    
    // 検索結果の表示
    function displaySearchResults(results) {
        if (results.length === 0) {
            pdfList.innerHTML = '<div class="alert alert-warning text-center"><i class="fas fa-exclamation-triangle"></i> 検索結果が見つかりません。</div>';
            return;
        }
        
        let html = '<div class="table-responsive"><table class="table table-striped table-hover"><thead><tr><th>フォルダ</th><th>注文番号</th><th>ファイル名</th><th>操作</th></tr></thead><tbody>';
        
        results.forEach(pdf => {
            const badgeClass = pdf.folder === '01-000' ? 'bg-primary' : 'bg-info';
            html += `
                <tr>
                    <td><span class="badge ${badgeClass}">${pdf.folder}</span></td>
                    <td>${pdf.order_id}</td>
                    <td>${pdf.filename}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="/pdf/${pdf.order_id}/viewer" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> 表示
                            </a>
                            <a href="${pdf.url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> 新しいタブ
                            </a>
                            <a href="${pdf.url}" download class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> DL
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        pdfList.innerHTML = html;
    }
    
    // イベントリスナー
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        location.reload();
    });
});
</script>
@endsection 