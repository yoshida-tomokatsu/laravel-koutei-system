<?php
echo "✅ FTPアップロードテスト成功！";
echo "<br>現在時刻: " . date('Y-m-d H:i:s');
echo "<br>サーバー: " . $_SERVER['HTTP_HOST'];
echo "<br>PHPバージョン: " . phpversion();
echo "<br>パス: " . __DIR__;
?> 