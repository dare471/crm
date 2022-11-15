<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geos', function (Blueprint $table) {
            $table->id();
            $table->integer('fid');
            $table->string('owner')->default(false);
            $table->string('cult')->default(false);
            $table->string('region')->default(false);
            $table->string('district')->default(false);
            $table->integer('area')->default(false);
            $table->string('year')->default(false);
            $table->string('kad_number')->default(false);
            $table->string('title')->default(false);
            $table->string('geometry')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geos');
    }
}
