<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('ContractGuid');
            $table->string('OrganizationName');
            $table->string('ContragentName');
            $table->string('ManagerGuid');
            $table->string('RegionGuid');
            $table->string('SeasonGuid');
            $table->string('WarehouseGuid');
            $table->string('Currency');
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
        Schema::dropIfExists('contracts');
    }
}
