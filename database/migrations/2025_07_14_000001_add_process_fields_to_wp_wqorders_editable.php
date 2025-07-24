<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessFieldsToWpWqordersEditable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wp_wqorders_editable', function (Blueprint $table) {
            // 注文管理
            $table->unsignedBigInteger('order_handler_id')->nullable()->comment('注文担当者ID');
            $table->date('image_sent_date')->nullable()->comment('イメージ送付日');
            
            // 支払関連
            $table->unsignedBigInteger('payment_method_id')->nullable()->comment('支払方法ID');
            $table->date('payment_completed_date')->nullable()->comment('支払完了日');
            
            // プリント工程
            $table->unsignedBigInteger('print_factory_id')->nullable()->comment('プリント工場ID');
            $table->date('print_request_date')->nullable()->comment('プリント依頼日');
            $table->date('print_deadline')->nullable()->comment('プリント納期');
            
            // 縫製工程
            $table->unsignedBigInteger('sewing_factory_id')->nullable()->comment('縫製工場ID');
            $table->date('sewing_request_date')->nullable()->comment('縫製依頼日');
            $table->date('sewing_deadline')->nullable()->comment('縫製納期');
            
            // 品質検査・発送
            $table->date('quality_check_date')->nullable()->comment('品質検査日');
            $table->date('shipping_date')->nullable()->comment('発送日');
            
            // 外部キー制約（存在しない場合のみ）
            if (!Schema::hasColumn('wp_wqorders_editable', 'order_handler_id')) {
                $table->foreign('order_handler_id')->references('id')->on('order_handlers')->onDelete('set null');
            }
            if (!Schema::hasColumn('wp_wqorders_editable', 'payment_method_id')) {
                $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            }
            if (!Schema::hasColumn('wp_wqorders_editable', 'print_factory_id')) {
                $table->foreign('print_factory_id')->references('id')->on('print_factories')->onDelete('set null');
            }
            if (!Schema::hasColumn('wp_wqorders_editable', 'sewing_factory_id')) {
                $table->foreign('sewing_factory_id')->references('id')->on('sewing_factories')->onDelete('set null');
            }
        });
        
        // インデックス追加
        Schema::table('wp_wqorders_editable', function (Blueprint $table) {
            $table->index('order_handler_id');
            $table->index('payment_method_id'); 
            $table->index('print_factory_id');
            $table->index('sewing_factory_id');
            $table->index('image_sent_date');
            $table->index('payment_completed_date');
            $table->index('print_request_date');
            $table->index('sewing_request_date');
            $table->index('quality_check_date');
            $table->index('shipping_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wp_wqorders_editable', function (Blueprint $table) {
            // 外部キー制約削除
            $table->dropForeign(['order_handler_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['print_factory_id']);
            $table->dropForeign(['sewing_factory_id']);
            
            // インデックス削除
            $table->dropIndex(['order_handler_id']);
            $table->dropIndex(['payment_method_id']);
            $table->dropIndex(['print_factory_id']);
            $table->dropIndex(['sewing_factory_id']);
            $table->dropIndex(['image_sent_date']);
            $table->dropIndex(['payment_completed_date']);
            $table->dropIndex(['print_request_date']);
            $table->dropIndex(['sewing_request_date']);
            $table->dropIndex(['quality_check_date']);
            $table->dropIndex(['shipping_date']);
            
            // カラム削除
            $table->dropColumn([
                'order_handler_id',
                'image_sent_date',
                'payment_method_id',
                'payment_completed_date',
                'print_factory_id',
                'print_request_date',
                'print_deadline',
                'sewing_factory_id',
                'sewing_request_date',
                'sewing_deadline',
                'quality_check_date',
                'shipping_date'
            ]);
        });
    }
} 