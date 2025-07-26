<?php
// 本番環境でのデータベース修正スクリプト
// 本番サーバーで実行: php production_fix.php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== Production Environment Fix ===\n\n";
    
    // 1. データベース接続テスト
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
    
    // 2. 必要なテーブルの存在確認
    echo "2. Checking required tables...\n";
    $tables = ['users', 'wp_wqorders_editable'];
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            echo "✅ Table '{$table}' exists\n";
        } else {
            echo "❌ Table '{$table}' missing\n";
        }
    }
    echo "\n";
    
    // 3. usersテーブルのカラム確認と修正
    echo "3. Checking users table structure...\n";
    
    // passwordカラムの確認と追加
    if (!Schema::hasColumn('users', 'password')) {
        echo "Adding password column to users table...\n";
        DB::statement('ALTER TABLE users ADD COLUMN password VARCHAR(255) NULL');
        echo "✅ Password column added\n";
    } else {
        echo "✅ Password column exists\n";
    }
    
    // user_idカラムの確認と追加
    if (!Schema::hasColumn('users', 'user_id')) {
        echo "Adding user_id column to users table...\n";
        DB::statement('ALTER TABLE users ADD COLUMN user_id VARCHAR(255) UNIQUE NULL');
        echo "✅ User_id column added\n";
    } else {
        echo "✅ User_id column exists\n";
    }
    
    // roleカラムの確認と追加
    if (!Schema::hasColumn('users', 'role')) {
        echo "Adding role column to users table...\n";
        DB::statement('ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT \'user\'');
        echo "✅ Role column added\n";
    } else {
        echo "✅ Role column exists\n";
    }
    
    // 4. wp_wqorders_editableテーブルのカラム確認と修正
    echo "\n4. Checking wp_wqorders_editable table structure...\n";
    
    $requiredColumns = [
        'notes' => 'TEXT NULL',
        'last_updated' => 'INT(11) NULL',
        'order_handler_id' => 'INT(11) NULL',
        'image_sent_date' => 'DATE NULL',
        'payment_method_id' => 'INT(11) NULL',
        'payment_completed_date' => 'DATE NULL',
        'print_factory_id' => 'INT(11) NULL',
        'print_request_date' => 'DATE NULL',
        'print_deadline' => 'DATE NULL',
        'sewing_factory_id' => 'INT(11) NULL',
        'sewing_request_date' => 'DATE NULL',
        'sewing_deadline' => 'DATE NULL',
        'quality_check_date' => 'DATE NULL',
        'shipping_date' => 'DATE NULL'
    ];
    
    foreach ($requiredColumns as $column => $definition) {
        if (!Schema::hasColumn('wp_wqorders_editable', $column)) {
            echo "Adding {$column} column...\n";
            DB::statement("ALTER TABLE wp_wqorders_editable ADD COLUMN {$column} {$definition}");
            echo "✅ {$column} column added\n";
        } else {
            echo "✅ {$column} column exists\n";
        }
    }
    
    // 5. パスワードの設定（必要に応じて）
    echo "\n5. Checking user passwords...\n";
    $usersWithoutPassword = DB::table('users')
        ->whereNull('password')
        ->orWhere('password', '')
        ->get();
    
    if ($usersWithoutPassword->count() > 0) {
        echo "Setting default passwords for users without passwords...\n";
        foreach ($usersWithoutPassword as $user) {
            $password = $user->user_id === 'admin' ? 'password' : 'employee123';
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => Hash::make($password)]);
            echo "✅ Password set for user: {$user->user_id}\n";
        }
    } else {
        echo "✅ All users have passwords set\n";
    }
    
    echo "\n🎉 Production environment fix completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Clear Laravel caches: php artisan config:clear\n";
    echo "2. Clear routes: php artisan route:clear\n";
    echo "3. Clear views: php artisan view:clear\n";
    echo "4. Test the application: visit your production URL\n";
    
} catch (\Exception $e) {
    echo "❌ Error occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 