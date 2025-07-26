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
            'password' => Hash::make('password'),
            'name' => '管理者',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        // 従業員ユーザー
        User::create([
            'user_id' => 'employee',
            'password' => Hash::make('employee123'),
            'name' => '従業員',
            'email' => 'employee@example.com',
            'role' => 'employee'
        ]);
    }
} 