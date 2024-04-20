<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_files', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_date');
            $table->timestamp('updated_date')->nullable();
            $table->timestamp('deactivated')->nullable();
            $table->uuid('created_by');
            $table->string('file_name');
            $table->string('source');
            $table->string('type');
            $table->unsignedInteger('element')->nullable(); // Assume 'element' is optional
            $table->foreign('created_by')->references('id')->on('users');
     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_files');
    }
}
