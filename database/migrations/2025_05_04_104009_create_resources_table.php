<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->comment('computer or zone');
            $table->string('category')->nullable(); // Для компьютеров (Standard, VIP, Bootcamp)
            $table->string('status')->default('available');
            $table->json('specifications')->nullable(); // Для компьютеров
            $table->json('equipment')->nullable(); // Для зон
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resources');
    }
}
