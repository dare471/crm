<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('sign_in');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->integer('client_id')->nullable();
            $table->integer('product_id');
            $table->float('count');
            $table->float('price');
            $table->boolean('actual_price');
            $table->float('recomendated_price');
            $table->uuid('delivery_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail');
    }
}
