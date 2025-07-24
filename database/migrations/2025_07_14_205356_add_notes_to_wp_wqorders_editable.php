<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToWpWqordersEditable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wp_wqorders_editable', function (Blueprint $table) {
            // 備考カラム
            if (!Schema::hasColumn('wp_wqorders_editable', 'notes')) {
                $table->text('notes')->nullable()->comment('備考');
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
            // カラム削除
            if (Schema::hasColumn('wp_wqorders_editable', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
}
