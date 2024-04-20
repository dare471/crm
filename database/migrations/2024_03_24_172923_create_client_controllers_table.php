<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientControllersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор
            $table->string('iin'); // Индивидуальный идентификационный номер
            $table->string('name'); // Имя клиента
            $table->text('address'); // Адрес клиента
            $table->unsignedBigInteger('katoId'); // ID КАТО (Код Административно-Территориальных Объектов)
            $table->timestamps(); // Поля created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client');
    }
}
