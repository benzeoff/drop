<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingsTable extends Migration
{
    public function up()
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // computer or zone
            $table->string('category'); // Standard, VIP, Bootcamp, Main Hall, Chill Zone, TV
            $table->string('package_type'); // standard, day, night
            $table->integer('duration'); // в минутах
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricings');
    }
}
