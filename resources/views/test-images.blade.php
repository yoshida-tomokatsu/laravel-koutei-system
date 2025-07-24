<!DOCTYPE html>
<html>
<head>
    <title>画像表示テスト - 注文ID {{ $orderId }}</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
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
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .image-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .image-info {
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .no-images {
            text-align: center;
            color: #999;
            font-style: italic;
            margin-top: 50px;
        }
        .back-button {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>画像表示テスト</h1>
            <p>注文ID: {{ $orderId }}</p>
        </div>

        <a href="{{ route('test-files') }}" class="back-button">← 戻る</a>

        @if(count($images) > 0)
            <div class="image-gallery">
                @foreach($images as $image)
                    <div class="image-item">
                        <img src="{{ asset('uploads/' . $orderId . '/' . $image) }}" alt="{{ $image }}">
                        <div class="image-info">{{ $image }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-images">
                <h3>画像がありません</h3>
                <p>注文ID {{ $orderId }} に関連する画像ファイルが見つかりませんでした。</p>
                <p>uploads/{{ $orderId }}/ ディレクトリに画像ファイルを配置してください。</p>
            </div>
        @endif
    </div>
</body>
</html> 