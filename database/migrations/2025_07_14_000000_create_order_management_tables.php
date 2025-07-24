<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added missing import for DB facade

class CreateOrderManagementTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 注文担当者テーブル
        Schema::create('order_handlers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('担当者名');
            $table->string('email', 255)->nullable()->comment('メールアドレス');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->tinyInteger('is_active')->default(1)->comment('有効フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        // 支払方法テーブル
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('支払方法名');
            $table->string('description', 255)->nullable()->comment('説明');
            $table->tinyInteger('is_active')->default(1)->comment('有効フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        // プリント工場テーブル
        Schema::create('print_factories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('工場名');
            $table->text('address')->nullable()->comment('住所');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->string('email', 255)->nullable()->comment('メールアドレス');
            $table->tinyInteger('is_active')->default(1)->comment('有効フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        // 縫製工場テーブル
        Schema::create('sewing_factories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('工場名');
            $table->text('address')->nullable()->comment('住所');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->string('email', 255)->nullable()->comment('メールアドレス');
            $table->tinyInteger('is_active')->default(1)->comment('有効フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        // 初期データ挿入
        $this->insertDefaultData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sewing_factories');
        Schema::dropIfExists('print_factories');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('order_handlers');
    }

    /**
     * Insert default data.
     */
    private function insertDefaultData()
    {
        // 支払方法初期データ
        DB::table('payment_methods')->insert([
            ['name' => 'クレジットカード', 'description' => 'クレジットカード決済', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '銀行振込み', 'description' => '銀行振込決済', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '代引き', 'description' => '代金引換', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 担当者初期データ
        DB::table('order_handlers')->insert([
            ['name' => 'システム管理者', 'email' => 'admin@example.com', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
} 