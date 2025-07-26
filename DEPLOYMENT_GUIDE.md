# 工程管理システム - デプロイメント・引継ぎガイド

## 📋 **プロジェクト概要**

- **プロジェクト名**: 工程管理システム (Laravel)
- **本番URL**: https://koutei.kiryu-factory.com
- **開発環境**: Laravel 8.x + MySQL
- **リポジトリ**: https://github.com/yoshida-tomokatsu/laravel-koutei-system

## 🔧 **本番環境設定**

### **データベース設定**
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=factory0328_wp2
DB_USERNAME=factory0328_wp2
DB_PASSWORD=ctwjr3mmf5
```

### **アプリケーション設定**
```env
APP_NAME="工程管理システム"
APP_ENV=production
APP_KEY=base64:qWKe8uFO6ALKta3hmDb42Bsi/gHppLn4S/MFQ4ZJiTg=
APP_DEBUG=false
APP_URL=https://koutei.kiryu-factory.com
```

⚠️ **重要**: APP_KEYは正しいLaravel標準の32バイトキーである必要があります。

## 🚀 **デプロイ手順**

### **1. ローカル開発からの更新**

```bash
# 1. 変更をコミット
git add .
git commit -m "機能追加・修正の説明"

# 2. GitHubにプッシュ
git push origin main

# 3. 本番サーバーで更新
# (本番サーバーにSSH接続後)
cd /path/to/laravel-project
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### **緊急時の修正スクリプト**

#### **🚨 404エラー継続中の場合（最優先）**

**問題**: すべてのPHPファイルが404エラーになる
**原因**: サーバー設定、ドキュメントルート、PHP設定の根本的問題

**対応手順**:

1. **GitHubから以下のファイルをダウンロード**:
   - `URGENT_FIX.php` (最小PHPテスト)
   - `test.html` (HTMLテスト)
   - `one_click_fix.php` (自動修正スクリプト)

2. **cPanelで以下のすべての場所にアップロード**:
   ```
   /public_html/URGENT_FIX.php
   /public_html/test.html
   /public_html/public/URGENT_FIX.php
   /public_html/public/test.html
   /public_html/koutei/URGENT_FIX.php
   /public_html/koutei/test.html
   /public_html/www/URGENT_FIX.php
   /public_html/www/test.html
   ```

3. **以下のURLを順番にテスト**:
   ```
   https://koutei.kiryu-factory.com/test.html
   https://koutei.kiryu-factory.com/URGENT_FIX.php
   https://koutei.kiryu-factory.com/public/test.html
   https://koutei.kiryu-factory.com/public/URGENT_FIX.php
   https://koutei.kiryu-factory.com/koutei/test.html
   https://koutei.kiryu-factory.com/koutei/URGENT_FIX.php
   ```

4. **どれか1つでも成功した場所に `one_click_fix.php` をアップロード**

5. **成功した場所のURLで `one_click_fix.php` を実行**

#### **Laravel起動しない場合（最優先）**

1. **`emergency_check.php`をアップロード**
   - FTPまたはcPanelファイルマネージャーでアップロード
   - アップロード先: `/public_html/emergency_check.php`

2. **ブラウザで診断実行**
   ```
   https://koutei.kiryu-factory.com/emergency_check.php
   ```

3. **診断結果を確認して問題を特定**

#### **500エラーが発生した場合**

1. **`production_env_fix.php`をアップロード**
   - FTPまたはcPanelファイルマネージャーでアップロード
   - アップロード先: `/public_html/production_env_fix.php`

2. **ブラウザで実行**
   ```
   https://koutei.kiryu-factory.com/production_env_fix.php
   ```

3. **「🚀 本番環境修正を実行する」をクリック**

4. **修正完了後、ファイルを削除**

## 🛠 **トラブルシューティング**

### **よくある問題と解決方法**

#### **1. Laravel起動しない（白画面・404エラー）**
- **原因**: vendor/ディレクトリ不足、.env設定ミス、Laravel構造問題
- **解決**: `emergency_check.php`で診断 → 問題特定 → 対応

#### **2. 500 Internal Server Error**
- **原因**: データベース接続エラー、Laravel設定ミス、APP_KEY問題
- **解決**: 
  - `production_env_fix.php`を実行
  - APP_KEYエラーの場合: `app_key_fix.php`または`production_app_key_fix.php`を実行

#### **3. ログインできない**
- **原因**: usersテーブルの構造不整合
- **解決**: 管理者ユーザー情報
  - **ユーザーID**: `admin`
  - **パスワード**: `password`

#### **4. データベース接続エラー**
- **確認事項**:
  - データベース名: `factory0328_wp2`
  - ユーザー名: `factory0328_wp2`
  - パスワード: `ctwjr3mmf5`

#### **5. 注文保存エラー**
- **原因**: `wp_wqorders_editable`テーブルの`notes`カラム不足
- **解決**: 修正スクリプトで自動追加

## 📁 **重要なファイル**

### **修正スクリプト一覧**
- `production_env_fix.php` - 本番環境用総合修正スクリプト
- `standalone_fix.php` - Laravel非依存の独立修正スクリプト
- `laravel_startup_fix.php` - Laravel起動問題修正スクリプト
- `emergency_check.php` - **緊急診断スクリプト（Laravel起動しない場合）**

### **データベース**
- `factory0328_wp2.sql` - 本番データベースのバックアップ

### **主要なLaravelファイル**
- `app/Http/Controllers/OrderController.php` - 注文管理コントローラー
- `app/Models/Order.php` - 注文モデル
- `app/Models/User.php` - ユーザーモデル
- `database/migrations/` - データベース構造定義
- `database/seeders/UserSeeder.php` - 初期ユーザーデータ

## 🔐 **認証情報**

### **管理者ログイン**
- **URL**: https://koutei.kiryu-factory.com/login
- **ユーザーID**: `admin`
- **パスワード**: `password`

### **従業員ログイン**
- **ユーザーID**: `employee`
- **パスワード**: `employee123`

### **その他のユーザー**
- **oshima**: `employee123`
- **yamada**: `employee123`

## 📊 **データベース構造**

### **主要テーブル**

#### **users**
```sql
- id (Primary Key)
- user_id (Unique) - ログイン用ID
- password - ハッシュ化パスワード
- name - 表示名
- email - メールアドレス
- role - 権限 (admin/employee)
- created_at, updated_at
```

#### **wp_wqorders_editable**
```sql
- id (Primary Key)
- formId - フォームID
- formTitle - フォームタイトル
- customer - 顧客ID
- total - 合計金額
- content - 注文内容(JSON)
- notes - 備考
- last_updated - 最終更新日時
- order_handler_id - 担当者ID
- (その他工程管理用カラム多数)
```

## 🚨 **緊急連絡先・注意事項**

### **重要な注意点**
1. **本番環境では必ず`APP_DEBUG=false`に設定**
2. **修正スクリプト実行後は必ずファイルを削除**
3. **データベースバックアップを定期的に取得**
4. **Git履歴を残すため、直接本番ファイル編集は避ける**

### **修正履歴**
- **2025/07/27**: データベース接続・ログイン機能修正
- **2025/07/27**: 注文保存エラー修正（notesカラム追加）
- **2025/07/27**: 本番環境500エラー対応スクリプト作成

## 📝 **今後の開発・保守**

### **推奨作業フロー**
1. **ローカル環境で開発・テスト**
2. **Gitにコミット・プッシュ**
3. **本番環境でpull & デプロイ**
4. **動作確認**

### **定期保守項目**
- データベースバックアップ
- ログファイルの確認・クリア
- セキュリティアップデート
- パフォーマンス監視

---

**最終更新**: 2025年7月27日  
**作成者**: AI Assistant  
**引継ぎ先**: 開発・保守担当者 