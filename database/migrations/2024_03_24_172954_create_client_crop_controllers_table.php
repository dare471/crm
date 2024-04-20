<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientCropControllersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_crop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientId');
            $table->string('area');
            $table->bigInteger('unit');
            $table->string('culture');
            $table->bigInteger('cultureId');
            $table->string('activitySubstance');
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
        Schema::dropIfExists('client_crop');
    }
}
