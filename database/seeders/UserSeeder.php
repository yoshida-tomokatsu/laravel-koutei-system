<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 管理者ユーザー
        User::create([
            'user_id' => 'admin',
            'password_hash' => Hash::make('password'),
            'name' => '管理者',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'permissions' => 'all',
            'is_active' => 1,
            'created_by' => 'system',
            'notes' => '管理者アカウント'
        ]);

        // 従業員ユーザー
        User::create([
            'user_id' => 'employee',
            'password_hash' => Hash::make('employee123'),
            'name' => '従業員',
            'email' => 'employee@example.com',
            'role' => 'employee',
            'permissions' => 'limited',
            'is_active' => 1,
            'created_by' => 'system',
            'notes' => '従業員アカウント'
        ]);
    }
} 