<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgriculturalMachinery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agricultural_machinery', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->string('type');  // Тип техники, например, трактор, комбайн и т.д.
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->year('manufacture_year');
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agricultural_machinery');
    }
}
