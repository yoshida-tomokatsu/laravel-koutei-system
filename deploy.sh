#!/bin/bash

# 工程管理システム - 本番環境デプロイスクリプト

echo "工程管理システムデプロイ開始..."

# 1. 依存関係のインストール
echo "Composer依存関係をインストール中..."
composer install --no-dev --optimize-autoloader

# 2. アプリケーションキーの生成
echo "アプリケーションキーを生成中..."
php artisan key:generate --force

# 3. 設定キャッシュの生成
echo "設定キャッシュを生成中..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. ディレクトリ権限の設定
echo "ディレクトリ権限を設定中..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 5. aforms-pdfディレクトリの確認
echo "PDF ディレクトリを確認中..."
if [ ! -d "../aforms-pdf" ]; then
    echo "警告: aforms-pdfディレクトリが見つかりません"
    echo "PDFファイルの配置を確認してください"
fi

# 6. データベース接続の確認
echo "データベース接続を確認中..."
php artisan migrate:status

# 7. 本番環境設定の確認
echo "本番環境設定を確認中..."
echo "APP_ENV: $(php artisan tinker --execute='echo config("app.env");')"
echo "APP_DEBUG: $(php artisan tinker --execute='echo config("app.debug") ? "true" : "false";')"
echo "APP_URL: $(php artisan tinker --execute='echo config("app.url");')"

echo "デプロイ完了!"
echo "URL: https://koutei.kiryu-factory.com"
echo "管理者: admin/password"
echo "従業員: employee/employee123" 