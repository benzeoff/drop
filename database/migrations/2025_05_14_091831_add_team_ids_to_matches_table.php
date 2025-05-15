<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('player1_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('player2_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('team1_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('team2_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->integer('player1_score')->nullable();
            $table->integer('player2_score')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
    }
};
