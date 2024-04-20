<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSentEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_emails', function (Blueprint $table) {
            $table->id();
            $table->string('orderGuid')->unique();
            $table->string('order')->unique();
            $table->string('clientName');
            $table->string('iinBin');
            $table->string('dateStatus');
            $table->string('tel');
            $table->string('email');
            $table->string('type');
            $table->boolean('sent')->default(false); // Допустим, это поле для отметки успешной отправки
            $table->string('created_at');
            $table->string('updated_at');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_emails');
    }
}
