<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesticidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesticides', function (Blueprint $table) {
            $table->id('id');
            $table->char('tradename');
            $table->char('consumption_preparation');
            $table->char('culture');
            $table->string('harmful_organism');
            $table->char('time_processing');
            $table->char('last_treatment');
            $table->integer('pest_id');
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
        Schema::dropIfExists('pesticides');
    }
}
