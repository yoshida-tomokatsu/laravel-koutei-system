@echo off
echo === Laravel サーバー起動スクリプト ===
echo.
echo 現在のディレクトリ: %CD%
echo.

echo データベース接続テスト中...
php test_connection.php
if %errorlevel% neq 0 (
    echo データベース接続に失敗しました。
    pause
    exit /b 1
)

echo.
echo Laravel サーバーを起動しています...
echo URL: http://localhost:8000/orders
echo.
echo 終了する場合は Ctrl+C を押してください。
echo.

php artisan serve --host=127.0.0.1 --port=8000 