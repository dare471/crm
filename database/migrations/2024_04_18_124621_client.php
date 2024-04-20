<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Client extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('bin')->unique();
            $table->string('email');
            $table->string('phone');
            $table->timestamp('phone_verified_at')->nullable();
            $table->boolean('phone_verified_status')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_verified_status')->default(false);
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
