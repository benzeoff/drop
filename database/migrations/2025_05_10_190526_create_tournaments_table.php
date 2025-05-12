<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentsTable extends Migration
{
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название турнира
            $table->string('game'); // Игра (CS:GO, Dota 2 и т.д.)
            $table->dateTime('date'); // Дата и время турнира
            $table->text('description')->nullable(); // Описание
            $table->string('prize')->nullable(); // Призы
            $table->integer('max_participants'); // Макс. число участников
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}
