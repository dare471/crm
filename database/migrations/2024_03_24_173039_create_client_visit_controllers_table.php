<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientVisitControllersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_visit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientId');
            $table->unsignedBigInteger('createdBy');
            $table->timestamp('createdTime')->nullable();
            $table->timestamp('updatedTime')->nullable();
            $table->timestamp('plannedTime')->nullable();
            $table->timestamp('startTime')->nullable();
            $table->timestamp('finishTime')->nullable();
            $table->unsignedBigInteger('contactPersonId');
            $table->bigInteger('placeMeetingId');
            $table->string('placeMeetingDescription');
            $table->bigInteger('purposeOfMeeting');
            $table->string('purposeOfMeetingDescription');
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
        Schema::dropIfExists('client_visit');
    }
}
