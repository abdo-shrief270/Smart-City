<?php

namespace Database\Seeders;

use App\Models\ParkingSlot;
use Illuminate\Database\Seeder;

class ParkingSeeder extends Seeder
{
    public function run(): void
    {
        $slotsPerArea = 4;

        foreach (['A', 'B'] as $area) {
            for ($i = 1; $i <= $slotsPerArea; $i++) {
                ParkingSlot::firstOrCreate([
                    'area' => $area,
                    'slot_number' => $i,
                ], [
                    'status' => 'available',
                    'cost_per_hour' => 10.00,
                ]);
            }
        }
    }
}
