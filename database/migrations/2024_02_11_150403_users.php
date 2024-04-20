<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('bin')->unique();
            $table->string('email');
            $table->string('phone');
            $table->boolean('activated_status')->default(false);
            $table->timestamp('activated_status_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();    
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
        Schema::dropIfExists('users');
    }
}
