<?php

namespace Database\Seeders;

use App\Models\ParkingSlot;
use Illuminate\Database\Seeder;

class ParkingSeeder extends Seeder
{
    public function run(): void
    {
        // Area A (1-8)
        for ($i = 1; $i <= 8; $i++) {
            ParkingSlot::firstOrCreate([
                'area' => 'A',
                'slot_number' => $i,
            ], [
                'status' => 'available',
                'cost_per_hour' => 10.00,
            ]);
        }

        // Area B (1-8)
        for ($i = 1; $i <= 8; $i++) {
            ParkingSlot::firstOrCreate([
                'area' => 'B',
                'slot_number' => $i,
            ], [
                'status' => 'available',
                'cost_per_hour' => 10.00,
            ]);
        }
    }
}
