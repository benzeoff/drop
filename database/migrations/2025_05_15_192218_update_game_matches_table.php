<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGameMatchesTable extends Migration
{
    public function up()
    {
        Schema::table('game_matches', function (Blueprint $table) {
            // Добавляем новые столбцы для команд
            $table->foreignId('team1_id')->nullable()->after('player2_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('team2_id')->nullable()->after('team1_id')->constrained('teams')->onDelete('cascade');
            $table->integer('team1_score')->nullable()->after('team2_id');
            $table->integer('team2_score')->nullable()->after('team1_score');

            // Изменяем onDelete для player1_id и player2_id
            $table->dropForeign(['player1_id']);
            $table->dropForeign(['player2_id']);

            $table->foreignId('player1_id')
                ->nullable()
                ->change()
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('player2_id')
                ->nullable()
                ->change()
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('game_matches', function (Blueprint $table) {
            // Откат изменений для команд
            $table->dropColumn(['team1_id', 'team2_id', 'team1_score', 'team2_score']);

            // Восстановление оригинальных onDelete для игроков
            $table->dropForeign(['player1_id']);
            $table->dropForeign(['player2_id']);

            $table->foreignId('player1_id')
                ->nullable()
                ->change()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('player2_id')
                ->nullable()
                ->change()
                ->constrained('users')
                ->onDelete('set null');
        });
    }
}
