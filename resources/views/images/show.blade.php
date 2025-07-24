@extends('layouts.app')

@section('title', '画像一覧 - 注文ID: ' . $orderId)

@section('content')
<div class="container">
    <div class="header-section">
        <h1>画像一覧 - 注文ID: {{ $orderId }}</h1>
        <button onclick="window.close()" class="btn-close">閉じる</button>
    </div>

    @if(count($images) > 0)
        <div class="images-grid">
            @foreach($images as $image)
                <div class="image-item">
                    <div class="image-wrapper">
                        <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="preview-image">
                        <div class="image-overlay">
                            <button onclick="openImageModal('{{ $image['url'] }}', '{{ $image['name'] }}')" class="btn-view">
                                拡大表示
                            </button>
                            <a href="{{ $image['url'] }}" download="{{ $image['name'] }}" class="btn-download">
                                ダウンロード
                            </a>
                        </div>
                    </div>
                    <div class="image-info">
                        <div class="image-name">{{ $image['name'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-images">
            <p>画像ファイルがありません。</p>
        </div>
    @endif

    <!-- 画像モーダル -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeImageModal()">&times;</span>
            <img id="modalImage" class="modal-image" alt="">
            <div class="modal-info">
                <h3 id="modalImageName"></h3>
                <div class="modal-actions">
                    <button onclick="closeImageModal()" class="btn-modal-close">閉じる</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e9ecef;
    }

    .header-section h1 {
        margin: 0;
        color: #2c3e50;
        font-size: 24px;
    }

    .btn-close {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-close:hover {
        background-color: #5a6268;
    }

    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .image-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .image-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .image-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .image-wrapper:hover .image-overlay {
        opacity: 1;
    }

    .btn-view,
    .btn-download {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-view {
        background-color: #007bff;
        color: white;
    }

    .btn-download {
        background-color: #28a745;
        color: white;
    }

    .btn-view:hover {
        background-color: #0056b3;
    }

    .btn-download:hover {
        background-color: #218838;
    }

    .image-info {
        padding: 15px;
    }

    .image-name {
        font-weight: bold;
        color: #2c3e50;
        font-size: 14px;
        word-break: break-all;
    }

    .no-images {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        font-size: 18px;
    }

    /* モーダルのスタイル */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    .modal-image {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .modal-info {
        text-align: center;
        padding: 20px;
        color: white;
    }

    .modal-info h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
    }

    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: #bbb;
    }

    .btn-modal-close {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-modal-close:hover {
        background-color: #5a6268;
    }

    /* レスポンシブ対応 */
    @media only screen and (max-width: 700px) {
        .modal-content {
            width: 100%;
            margin: 50px auto;
        }
        
        .images-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    function openImageModal(imageUrl, imageName) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalImageName = document.getElementById('modalImageName');
        
        modal.style.display = 'block';
        modalImage.src = imageUrl;
        modalImageName.textContent = imageName;
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    // モーダルの外側をクリックした場合に閉じる
    window.onclick = function(event) {
        const modal = document.getElementById('imageModal');
        if (event.target === modal) {
            closeImageModal();
        }
    }

    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
@endsection 