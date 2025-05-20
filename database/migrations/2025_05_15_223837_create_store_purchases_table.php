<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('store_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->integer('points_spent');
            $table->string('status')->default('pending'); // e.g., pending, redeemed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_purchases');
    }
}
