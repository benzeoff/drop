<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedResourcesAndPricings extends Migration
{
    public function up()
    {
        // Заполнение ресурсов (computers)
        $computers = [
            [
                'name' => 'PC-Standard-1',
                'category' => 'Standard',
                'specifications' => '{"cpu": "Intel Core I5-9400F", "gpu": "RTX 3070", "ram": "HyperX Fury 16gb", "chair": "DXRacer DF73", "mouse": "Logitech G102", "headset": "HyperX Cloud II", "monitor": "AOC 144Hz 24\" ", "keyboard": "HyperX Alloy MKW100", "mousepad": "QCyber ultimate"}'
            ],
            [
                'name' => 'PC-Standard-2',
                'category' => 'Standard',
                'specifications' => '{"cpu": "Intel Core I5-9400F", "gpu": "RTX 3070", "ram": "HyperX Fury 16gb", "chair": "DXRacer DF73", "mouse": "Logitech G102", "headset": "HyperX Cloud II", "monitor": "AOC 144Hz 24\" ", "keyboard": "HyperX Alloy MKW100", "mousepad": "QCyber ultimate"}'
            ],
            [
                'name' => 'PC-VIP-1',
                'category' => 'VIP',
                'specifications' => '{"cpu": "Intel Core I5-9400F", "gpu": "RTX 2060", "ram": "HyperX Predator 16gb", "chair": "DXRacer G8200", "mouse": "Logitech G Pro Hero", "headset": "HyperX Cloud III", "monitor": "AOC 25G3ZM/BK Black 240Hz 24.5\" ", "mousepad": "GLHF"}'
            ],
            [
                'name' => 'PC-BOOTCAMP-1',
                'category' => 'BOOTCAMP',
                'specifications' => '{"cpu": "AMD Ryzen 5 3600X", "gpu": "RTX 2060", "ram": "HyperX Predator 16gb", "chair": "DXRacer DF73", "mouse": "Logitech G Pro Hero", "headset": "HyperX Cloud Alpha S", "monitor": "AOC 25G3ZM/BK Black 240Hz 24.5\" ", "mousepad": "GLHF"}'
            ],
        ];

        foreach ($computers as $computer) {
            DB::table('resources')->insert([
                'name' => $computer['name'],
                'type' => 'computer',
                'category' => $computer['category'],
                'status' => 'available',
                'specifications' => $computer['specifications'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Заполнение ресурсов (zones)
        $zones = [
            ['name' => 'Main Hall', 'equipment' => '{"tv": "2 x Haier 4K 55\" 120Hz"}'],
            ['name' => 'Chill Zone', 'equipment' => '{"screen": "150\" ", "projector": "Optoma UHL 4K"}'],
            ['name' => 'TV', 'equipment' => null],
        ];

        foreach ($zones as $zone) {
            DB::table('resources')->insert([
                'name' => $zone['name'],
                'type' => 'zone',
                'category' => $zone['name'],
                'status' => 'available',
                'equipment' => $zone['equipment'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Заполнение цен
        $pricings = [
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'standard', 'duration' => 30, 'price' => 5.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'standard', 'duration' => 60, 'price' => 8.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'day', 'duration' => 180, 'price' => 20.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'day', 'duration' => 300, 'price' => 30.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'day', 'duration' => 720, 'price' => 50.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'night', 'duration' => 180, 'price' => 25.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'night', 'duration' => 300, 'price' => 35.00],
            ['type' => 'computer', 'category' => 'Standard', 'package_type' => 'night', 'duration' => 720, 'price' => 60.00],
            ['type' => 'computer', 'category' => 'VIP', 'package_type' => 'standard', 'duration' => 30, 'price' => 7.00],
            ['type' => 'computer', 'category' => 'VIP', 'package_type' => 'standard', 'duration' => 60, 'price' => 10.00],
            ['type' => 'computer', 'category' => 'BOOTCAMP', 'package_type' => 'standard', 'duration' => 30, 'price' => 6.00],
            ['type' => 'computer', 'category' => 'BOOTCAMP', 'package_type' => 'standard', 'duration' => 60, 'price' => 9.00],
            ['type' => 'zone', 'category' => 'Main Hall', 'package_type' => 'standard', 'duration' => 30, 'price' => 10.00],
            ['type' => 'zone', 'category' => 'Chill Zone', 'package_type' => 'standard', 'duration' => 30, 'price' => 12.00],
            ['type' => 'zone', 'category' => 'TV', 'package_type' => 'standard', 'duration' => 30, 'price' => 8.00],
        ];

        foreach ($pricings as $pricing) {
            DB::table('pricings')->insert([
                'type' => $pricing['type'],
                'category' => $pricing['category'],
                'package_type' => $pricing['package_type'],
                'duration' => $pricing['duration'],
                'price' => $pricing['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('resources')->truncate();
        DB::table('pricings')->truncate();
    }
}
