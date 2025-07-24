@extends('layouts.app')

@section('content')
<div class="container-fluid" style="height: 100vh; display: flex; flex-direction: column;">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>見積りPDF表示 - 注文 {{ $orderId }}</h2>
                <div class="btn-group" role="group">
                    @if(count($pdfUrls) > 0)
                        <a href="{{ $pdfUrls[0]['url'] }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> 新しいタブで開く
                    </a>
                        <a href="{{ $pdfUrls[0]['url'] }}" download class="btn btn-success">
                        <i class="fas fa-download"></i> ダウンロード
                        </a>
                    @endif
                    <a href="{{ route('pdf.manage', $orderId) }}" class="btn btn-warning">
                        <i class="fas fa-cog"></i> 管理
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> 戻る
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(count($pdfUrls) > 1)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">PDFファイル一覧 ({{ count($pdfUrls) }}件)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($pdfUrls as $index => $pdf)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                {{ $pdf['name'] }}
                                                @if($pdf['type'] === 'main')
                                                    <span class="badge bg-primary">メイン</span>
                                                @else
                                                    <span class="badge bg-secondary">追加</span>
                                                @endif
                                            </h6>
                                            <div class="btn-group" role="group">
                                                <button data-pdf-url="{{ $pdf['url'] }}" class="btn btn-sm btn-primary load-pdf-btn">
                                                    表示
                                                </button>
                                                <a href="{{ $pdf['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    新しいタブ
                                                </a>
                                                <a href="{{ $pdf['url'] }}" download class="btn btn-sm btn-success">
                                                    DL
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row flex-grow-1">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-body p-0">
                    @if(count($pdfUrls) > 0)
                        <iframe id="pdfViewer" 
                                src="{{ $pdfUrls[0]['url'] }}" 
                                width="100%" 
                                height="100%" 
                                style="border: none; min-height: 600px;">
                            PDFの表示に対応していないブラウザです。
                            <a href="{{ $pdfUrls[0]['url'] }}" target="_blank">こちらからPDFを開いてください</a>
                        </iframe>
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <i class="fas fa-file-pdf fa-5x text-muted mb-3"></i>
                                <h4 class="text-muted">PDFファイルが見つかりません</h4>
                                <p class="text-muted">この注文には見積りPDFが登録されていません。</p>
                                <a href="{{ route('orders.index') }}" class="btn btn-primary">
                                    注文一覧に戻る
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadPdf(url) {
    const iframe = document.getElementById('pdfViewer');
    iframe.src = url;
}

// Add event listeners for load PDF buttons
document.addEventListener('DOMContentLoaded', function() {
    const loadPdfButtons = document.querySelectorAll('.load-pdf-btn');
    loadPdfButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-pdf-url');
            loadPdf(url);
        });
    });
});
</script>
@endsection 