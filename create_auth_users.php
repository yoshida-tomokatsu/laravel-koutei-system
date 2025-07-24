<?php
// Laravel認証ユーザー設定スクリプト
// SQLインポート後に実行して、admin/passwordとemployee/employee123を設定

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Laravel環境の初期化
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Laravel認証ユーザー設定開始 ===\n";

try {
    // 現在のusersテーブル構造を確認
    echo "1. usersテーブル構造を確認中...\n";
    $columns = DB::select('DESCRIBE users');
    
    $hasUserId = false;
    $hasPasswordHash = false;
    $hasPassword = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'user_id') $hasUserId = true;
        if ($column->Field === 'password_hash') $hasPasswordHash = true;
        if ($column->Field === 'password') $hasPassword = true;
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    // パスワードフィールドの決定
    $passwordField = $hasPasswordHash ? 'password_hash' : 'password';
    echo "  使用するパスワードフィールド: {$passwordField}\n";
    
    // 既存ユーザーの確認
    echo "\n2. 既存ユーザーを確認中...\n";
    if ($hasUserId) {
        $adminUser = DB::table('users')->where('user_id', 'admin')->first();
        $employeeUser = DB::table('users')->where('user_id', 'employee')->first();
    } else {
        $adminUser = DB::table('users')->where('name', 'admin')->orWhere('email', 'admin@example.com')->first();
        $employeeUser = DB::table('users')->where('name', 'employee')->orWhere('email', 'employee@example.com')->first();
    }
    
    // adminユーザーの設定
    echo "\n3. adminユーザーを設定中...\n";
    $adminData = [
        'name' => '管理者',
        'email' => 'admin@example.com',
        $passwordField => Hash::make('password'),
        'role' => 'admin',
        'is_active' => 1,
        'updated_at' => now()
    ];
    
    if ($hasUserId) {
        $adminData['user_id'] = 'admin';
    }
    
    if ($adminUser) {
        // 既存ユーザーを更新
        if ($hasUserId) {
            DB::table('users')->where('user_id', 'admin')->update($adminData);
        } else {
            DB::table('users')->where('id', $adminUser->id)->update($adminData);
        }
        echo "  adminユーザーを更新しました\n";
    } else {
        // 新規ユーザーを作成
        $adminData['created_at'] = now();
        DB::table('users')->insert($adminData);
        echo "  adminユーザーを作成しました\n";
    }
    
    // employeeユーザーの設定
    echo "\n4. employeeユーザーを設定中...\n";
    $employeeData = [
        'name' => '従業員',
        'email' => 'employee@example.com',
        $passwordField => Hash::make('employee123'),
        'role' => 'employee',
        'is_active' => 1,
        'updated_at' => now()
    ];
    
    if ($hasUserId) {
        $employeeData['user_id'] = 'employee';
    }
    
    if ($employeeUser) {
        // 既存ユーザーを更新
        if ($hasUserId) {
            DB::table('users')->where('user_id', 'employee')->update($employeeData);
        } else {
            DB::table('users')->where('id', $employeeUser->id)->update($employeeData);
        }
        echo "  employeeユーザーを更新しました\n";
    } else {
        // 新規ユーザーを作成
        $employeeData['created_at'] = now();
        DB::table('users')->insert($employeeData);
        echo "  employeeユーザーを作成しました\n";
    }
    
    // 結果の確認
    echo "\n5. 設定結果を確認中...\n";
    $users = DB::table('users')->select('*')->get();
    foreach ($users as $user) {
        $identifier = $hasUserId ? $user->user_id : $user->email;
        echo "  ユーザー: {$identifier} ({$user->name}) - 役割: {$user->role}\n";
    }
    
    echo "\n=== 設定完了 ===\n";
    echo "ログイン情報:\n";
    echo "管理者: admin / password\n";
    echo "従業員: employee / employee123\n";
    echo "\nLaravel画面で http://localhost:8000/login からアクセスしてください。\n";
    
} catch (Exception $e) {
    echo "エラーが発生しました: " . $e->getMessage() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
    
    // データベース接続エラーの場合のヒント
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "\n== トラブルシューティング ==\n";
        echo "1. XAMPPが起動しているか確認\n";
        echo "2. MySQLサービスが動作しているか確認\n";
        echo "3. データベース名 'factory0328_wp2' が存在するか確認\n";
    }
} 