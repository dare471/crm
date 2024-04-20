<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class Profile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('client_id');  // Использование uuid для внешнего ключа
            $table->string('regionId')->nullable();
            $table->string('districtId')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->string('affilated_company')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('created_at')->useCurrent(Carbon::now()->timestamp);
            $table->string('updated_at')->useCurrent(Carbon::now()->timestamp);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
