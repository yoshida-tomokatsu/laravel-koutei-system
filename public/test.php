<?php
// Laravel Server Test - 新しいバックアップ

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Laravel Server Test - 新しいバックアップ</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 40px; }";
echo ".success { color: #28a745; font-size: 24px; font-weight: bold; }";
echo ".info { color: #17a2b8; margin: 10px 0; }";
echo ".warning { color: #ffc107; }";
echo ".error { color: #dc3545; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1> Laravel Server Test - 新しいバックアップ</h1>";

echo "<div class=\"info\"><strong>Current Directory:</strong> " . __DIR__ . "</div>";
echo "<div class=\"info\"><strong>Expected:</strong> C:\\\\Users\\\\tmkty\\\\Desktop\\\\laravel-koutei\\\\public</div>";
echo "<div class=\"info\"><strong>Server Time:</strong> " . date("Y-m-d H:i:s") . "</div>";
echo "<div class=\"info\"><strong>PHP Version:</strong> " . phpversion() . "</div>";

// 正しいフォルダかチェック
if (strpos(__DIR__, "laravel-koutei") !== false) {
    echo "<div class=\"success\"> 正しいフォルダ（laravel-koutei）から起動中</div>";
} else {
    echo "<div class=\"error\"> 古いフォルダ（koutei）から起動中</div>";
}

echo "<p><a href=\"/\">Laravel Home</a></p>";

echo "</body>";
echo "</html>";
?>
