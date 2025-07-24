@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>見積りPDF管理 - 注文 {{ $orderId }}</h2>
                <div class="btn-group" role="group">
                    <a href="{{ route('pdf.viewer', $orderId) }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> 表示
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> 戻る
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload"></i> PDFアップロード
                    </h5>
                </div>
                <div class="card-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="pdfFile" class="form-label">PDFファイル</label>
                            <input type="file" class="form-control" id="pdfFile" name="pdf_file" accept=".pdf" required>
                            <div class="form-text">
                                最大ファイルサイズ: 10MB<br>
                                対応形式: PDF
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload"></i> アップロード
                        </button>
                    </form>
                    
                    <div id="uploadProgress" class="mt-3" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">アップロード中...</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> 現在のPDFファイル ({{ count($pdfFiles) }}件)
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($pdfFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ファイル名</th>
                                        <th>種類</th>
                                        <th>サイズ</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pdfFiles as $pdf)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                {{ $pdf['name'] }}
                                            </td>
                                            <td>
                                                @if($pdf['type'] === 'main')
                                                    <span class="badge bg-primary">メイン</span>
                                                @else
                                                    <span class="badge bg-secondary">追加</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $filePath = public_path($pdf['path']);
                                                    $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                                    $formattedSize = $fileSize > 0 ? number_format($fileSize / 1024, 1) . ' KB' : '不明';
                                                @endphp
                                                {{ $formattedSize }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pdf.show', $orderId) }}?file={{ urlencode($pdf['name']) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> 表示
                                                    </a>
                                                    <a href="{{ route('pdf.show', $orderId) }}?file={{ urlencode($pdf['name']) }}" 
                                                       download 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i> DL
                                                    </a>
                                                    <button data-filename="{{ $pdf['name'] }}" 
                                                            class="btn btn-sm btn-danger delete-pdf-btn">
                                                        <i class="fas fa-trash"></i> 削除
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-pdf fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">PDFファイルがありません</h5>
                            <p class="text-muted">左側のフォームからPDFファイルをアップロードしてください。</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = uploadProgress.querySelector('.progress-bar');

    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('pdfFile');
        const file = fileInput.files[0];
        
        if (!file) {
            showToast('ファイルを選択してください', 'error');
            return;
        }
        
        if (file.type !== 'application/pdf') {
            showToast('PDFファイルのみアップロード可能です', 'error');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            showToast('ファイルサイズは10MB以下にしてください', 'error');
            return;
        }
        
        uploadFile(file);
    });
    
    // Add event listeners for delete buttons
    const deletePdfButtons = document.querySelectorAll('.delete-pdf-btn');
    deletePdfButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            deletePdf(filename);
        });
    });
});

function uploadFile(file) {
    const formData = new FormData();
    formData.append('pdf_file', file);
    
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    
    uploadProgress.style.display = 'block';
    progressBar.style.width = '0%';
    
    fetch(`/pdf/{{ $orderId }}/upload`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        progressBar.style.width = '100%';
        return response.json();
    })
    .then(data => {
        uploadProgress.style.display = 'none';
        
        if (data.success) {
            showToast('PDFファイルがアップロードされました', 'success');
            // ページをリロードしてファイル一覧を更新
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast('アップロードに失敗しました: ' + (data.message || '不明なエラー'), 'error');
        }
    })
    .catch(error => {
        uploadProgress.style.display = 'none';
        console.error('Upload error:', error);
        showToast('アップロードに失敗しました', 'error');
    });
}

function deletePdf(filename) {
    if (!confirm(`PDFファイル「${filename}」を削除しますか？`)) {
        return;
    }
    
    fetch(`/pdf/{{ $orderId }}/delete`, {
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
            showToast('PDFファイルが削除されました', 'success');
            // ページをリロードしてファイル一覧を更新
            setTimeout(() => {
                location.reload();
            }, 1000);
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
    const toastContainer = document.getElementById('toast-container');
    
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
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
}

.btn-group .btn {
    margin-right: 0;
}

.progress {
    height: 20px;
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}
</style>
@endsection 