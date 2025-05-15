<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('name');
            $table->text('description')->nullable()->after('logo');
            $table->foreignId('captain_id')->nullable()->constrained('users')->onDelete('set null')->after('description');
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['captain_id']);
            $table->dropColumn(['logo', 'description', 'captain_id']);
        });
    }
};
