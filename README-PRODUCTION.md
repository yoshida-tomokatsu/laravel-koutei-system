# 工程管理システム - 本番環境デプロイガイド

## 概要
既存のPHPシステムからLaravelへ移行した工程管理システムです。

## 本番環境情報
- **URL**: https://koutei.kiryu-factory.com
- **サーバー**: sv14052.xserver.jp
- **データベース**: factory0328_wp2

## 認証情報
- **管理者**: admin / password
- **従業員**: employee / employee123

## デプロイ手順

### 1. 事前準備
```bash
# 本番環境用の設定ファイルを作成
cp production-config.txt .env
# APP_KEYを生成
php artisan key:generate
```

### 2. 自動デプロイ（GitHub Actions）
```bash
# masterブランチにpush
git push origin master
```

### 3. 手動デプロイ
```bash
# FTPでファイルをアップロード
# アップロード先: /kiryu-factory.com/public_html/koutei/
# デプロイスクリプト実行
./deploy.sh
```

## ファイル配置

### Laravelアプリケーション
```
/kiryu-factory.com/public_html/koutei/
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
└── vendor/
```

### PDFファイル
```
/kiryu-factory.com/public_html/koutei/
├── aforms-pdf/
│   ├── 01-000/     # 既存PDFファイル
│   ├── 01-001/     # 既存PDFファイル
│   └── tmp/        # 一時PDFファイル
```

## 主要機能

### 1. 注文管理
- 注文一覧表示
- インライン編集機能
- 検索・フィルタリング
- エクスポート機能

### 2. PDF表示
- 注文IDベースでPDF表示
- 複数ディレクトリ対応
- セキュアなアクセス制御

### 3. 認証システム
- 管理者・従業員の役割ベース認証
- Laravel Breeze使用

## データベース設定

### 接続情報
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=factory0328_wp2
DB_USERNAME=factory0328_wp2
DB_PASSWORD=ctwjr3mmf5
```

### 主要テーブル
- `wp_wqorders_editable`: 注文編集データ
- `users`: 認証ユーザー情報

## セキュリティ

### PDFファイルアクセス
- 直接アクセス拒否（.htaccess）
- Laravel経由でのみアクセス可能
- 認証必須

### 設定
- `APP_DEBUG=false`（本番環境）
- HTTPS強制
- CSRF保護

## トラブルシューティング

### 1. データベース接続エラー
```bash
# 接続テスト
php artisan migrate:status
```

### 2. PDFファイルが表示されない
```bash
# ディレクトリ権限確認
ls -la aforms-pdf/
```

### 3. 認証が機能しない
```bash
# キャッシュクリア
php artisan config:clear
php artisan cache:clear
```

## 更新履歴
- 2025-01-07: Laravel移行完了
- 2025-01-07: PDF表示機能実装
- 2025-01-07: 編集機能実装
- 2025-01-07: 本番環境デプロイ準備完了

## 連絡先
- 開発者: AI Assistant
- 運用担当: 桐生ファクトリー 