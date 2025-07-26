<?php
echo "🔍 サーバーパス確認テスト<br>";
echo "現在のディレクトリ: " . __DIR__ . "<br>";
echo "ドキュメントルート: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "サーバー名: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "リクエストURI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "ファイルパス: " . __FILE__ . "<br>";
echo "<br>✅ このファイルが表示されれば、この場所が正しいアップロード先です！";
?> 