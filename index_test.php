<?php
echo "✅ アクセステスト成功！";
echo "<br>現在のディレクトリ: " . __DIR__;
echo "<br>ファイルパス: " . __FILE__;
echo "<br>サーバー: " . $_SERVER['HTTP_HOST'];
echo "<br>リクエストURI: " . $_SERVER['REQUEST_URI'];
?> 