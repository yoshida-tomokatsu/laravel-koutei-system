# CLAUDE.md

このファイルは、Claude Code (claude.ai/code) がこのリポジトリで作業する際のガイダンスを提供します。

## プロジェクト概要
これは桐生ファクトリー向けのLaravel ベース受注管理システム（「工程管理システム」）で、PHP から Laravel 8 に移行されました。テキスタイル製造業の受注、PDF ドキュメント、および生産ワークフローを管理します。

## よく使うコマンド

### 開発用
```bash
# Start development server
php artisan serve

# Install dependencies
composer install
npm install

# Frontend development
npm run dev           # Development build
npm run watch         # Watch for changes
npm run hot          # Hot module replacement
npm run prod         # Production build

# Database
php artisan migrate          # Run migrations
php artisan migrate:fresh    # Fresh database
php artisan db:seed         # Run seeders
```

### テスト・デバッグ用
```bash
# Run tests
php artisan test
./vendor/bin/phpunit

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Generate application key
php artisan key:generate
```

### 本番デプロイ
```bash
# Automatic deployment via GitHub Actions to main branch
git push origin main

# Manual deployment
./deploy.sh
```

## Architecture & Structure

### Core Models
- **Order** (`wp_wqorders_editable` table): Main order entity with complex JSON content parsing
- **OrderHandler, PaymentMethod, PrintFactory, SewingFactory**: Manufacturing process entities
- **User**: Authentication with custom user_id field

### Key Controllers
- **OrderController**: Order CRUD operations, inline editing, PDF/image management
- **PdfController**: PDF viewing, management, upload, and organization
- **OrderManagementController**: Production workflow management APIs

### Database Schema
- Uses existing WordPress-based table `wp_wqorders_editable` 
- Complex JSON content field parsing for customer data extraction
- Process tracking fields (dates for each workflow stage)
- Authentication via `users` table with roles (admin/employee)

### PDF Management System
- PDFs stored in `/public/aforms-pdf/` with folder-based organization:
  - `01-000/`: Orders 483-999
  - `01-001/`: Orders 1001-1313
  - `tmp/`: Temporary PDFs
- Supports multiple PDFs per order with automatic file discovery
- Order-based file naming with ID padding (5 digits)

### Frontend Architecture
- Laravel Breeze for authentication
- Blade templating with Tailwind CSS
- JavaScript modules in `/resources/js/`:
  - `order-management.js`: Order list functionality
  - `pdf-management.js`: PDF viewer and management
  - `ui-utilities.js`: Common UI helpers

### Route Structure
- **Web routes**: Dashboard, orders, PDF management (some routes temporarily unprotected)
- **Management routes**: Production workflow APIs (`/management/api/`)
- **PDF routes**: Viewing, uploading, management (`/pdf/`)
- **Auth routes**: Laravel Breeze authentication

## Environment Configuration

### Database Connection
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=factory0328_wp2
DB_USERNAME=factory0328_wp2
DB_PASSWORD=ctwjr3mmf5
```

### Production Environment
- **URL**: https://koutei.kiryu-factory.com
- **Server**: sv14052.xserver.jp (X-Server)
- **Deployment**: GitHub Actions automatic deployment

### Test Users
- **Admin**: admin / password
- **Employee**: employee / employee123

## 作業記録について

### 作業ログ記録要件
このプロジェクトで実施した全ての作業は`引き継ぎ資料_Laravel移行.md`に記録してください。記録対象は以下の通りです：
- 問題解決手順
- 環境設定変更
- バグ修正と解決策
- 新機能実装
- 設定更新
- データベース変更
- デプロイ修正

重要な作業完了後は速やかに引き継ぎ資料を更新し、プロジェクトの継続性を維持してください。

## 重要な注意事項

### 受注IDシステム
- 受注は#プレフィックス付きフォーマットID（例：#0001）を使用
- 複雑なPDFファイル発見ロジックがID不一致を処理
- ファイル場所の複数検索パターン（4桁、5桁パディング）

### コンテンツ解析
Orderモデルには以下を抽出する広範なJSON content解析メソッドが含まれます：
- 顧客情報（名前、メール、電話、住所）
- 会社詳細
- 納期
- 公開許可
- 製品カテゴリ（フォームタイトルからの自動検出）

### セキュリティ・アクセス制御
- Laravelルーティング経由でPDFの直接アクセスを保護
- ほとんどの機能で認証が必要
- CSRF保護有効
- 本番環境ではデバッグ無効

### ファイル管理
- `/public/uploads/{order_id}/` での画像アップロード
- 並び替え、リネーム機能付きPDF管理
- ファイル発見のフォールバック機構

## 移行状況
このシステムは既存PHPコードベースから移行されました。主なレガシー要素：
- WordPressテーブル構造互換性の維持
- 既存PDFファイル組織の保持
- 既存ユーザー認証データのサポート
- 後方互換性のための複雑なcontentフィールドJSON構造