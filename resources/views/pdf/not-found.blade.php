@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">PDF表示エラー</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-file-pdf fa-5x text-danger"></i>
                    </div>
                    
                    <h5 class="text-danger">PDFファイルが見つかりません</h5>
                    
                    <p class="text-muted">
                        注文番号 <strong>{{ $orderId }}</strong> に関連するPDFファイルが見つかりませんでした。
                    </p>
                    
                    <div class="alert alert-info">
                        <h6>考えられる原因:</h6>
                        <ul class="text-start">
                            <li>PDFファイルがまだ作成されていない</li>
                            <li>ファイルが別の場所に保存されている</li>
                            <li>注文番号が正しくない</li>
                            <li>ファイルが削除されている</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('pdf.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> PDF一覧を確認
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 注文管理に戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 