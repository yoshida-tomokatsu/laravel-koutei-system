<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wp_wqorders_editable', function (Blueprint $table) {
            $table->id();
            $table->integer('formId')->nullable();
            $table->string('formTitle', 100)->nullable();
            $table->integer('customer')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->bigInteger('created')->nullable();
            $table->text('content')->nullable();
            $table->integer('last_updated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wp_wqorders_editable');
    }
}
