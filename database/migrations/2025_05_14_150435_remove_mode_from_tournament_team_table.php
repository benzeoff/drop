<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournament_team', function (Blueprint $table) {
            $table->dropColumn('mode');
        });
    }

    public function down()
    {
        Schema::table('tournament_team', function (Blueprint $table) {
            $table->enum('mode', ['1v1', 'team'])->default('1v1');
        });
    }
};
