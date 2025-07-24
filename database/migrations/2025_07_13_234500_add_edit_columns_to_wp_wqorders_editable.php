<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditColumnsToWpWqordersEditable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wp_wqorders_editable', function (Blueprint $table) {
            // 編集日時カラム
            if (!Schema::hasColumn('wp_wqorders_editable', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->comment('編集日時');
            }
            
            // 編集者カラム
            if (!Schema::hasColumn('wp_wqorders_editable', 'edited_by')) {
                $table->string('edited_by', 255)->nullable()->comment('編集者');
            }
            
            // 編集フラグカラム
            if (!Schema::hasColumn('wp_wqorders_editable', 'is_edited')) {
                $table->tinyInteger('is_edited')->default(0)->comment('編集済みフラグ');
            }
        });
        
        // インデックス追加
        Schema::table('wp_wqorders_editable', function (Blueprint $table) {
            if (!Schema::hasColumn('wp_wqorders_editable', 'edited_at')) {
                $table->index('edited_at', 'idx_wp_wqorders_editable_edited_at');
            }
            if (!Schema::hasColumn('wp_wqorders_editable', 'is_edited')) {
                $table->index('is_edited', 'idx_wp_wqorders_editable_is_edited');
            }
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
            // インデックス削除
            $table->dropIndex('idx_wp_wqorders_editable_edited_at');
            $table->dropIndex('idx_wp_wqorders_editable_is_edited');
            
            // カラム削除
            $table->dropColumn(['edited_at', 'edited_by', 'is_edited']);
        });
    }
} 