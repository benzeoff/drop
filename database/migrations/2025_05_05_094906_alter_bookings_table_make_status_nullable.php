<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookingsTableMakeStatusNullable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Делаем поле status nullable
            $table->string('status')->nullable()->default(null)->change();
        });

        // Очищаем статусы confirmed, так как они теперь вычисляются
        \App\Models\Booking::where('status', 'confirmed')->update(['status' => null]);
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Возвращаем status к обязательному полю с дефолтным значением
            $table->string('status')->nullable(false)->default('confirmed')->change();
        });

        // Восстанавливаем статус confirmed для всех записей без cancelled
        \App\Models\Booking::whereNull('status')->update(['status' => 'confirmed']);
    }
}
