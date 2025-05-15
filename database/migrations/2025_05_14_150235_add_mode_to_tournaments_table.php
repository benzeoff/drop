<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->enum('mode', ['1v1', 'team'])->default('1v1')->after('max_participants');
        });
    }

    public function down()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('mode');
        });
    }
};
