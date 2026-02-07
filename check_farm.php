<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Smart Farm Data...\n";
$count = \App\Models\SmartFarmData::count();
echo "Total Records: " . $count . "\n";

if ($count > 0) {
    $latest = \App\Models\SmartFarmData::latest()->first();
    echo "Latest Record:\n";
    echo "  ID: " . $latest->id . "\n";
    echo "  Temp: " . $latest->temp . "°C\n";
    echo "  Humidity: " . $latest->humidity . "%\n";
    echo "  Pump: " . ($latest->is_pump_on ? 'ON' : 'OFF') . "\n";
    echo "  Created At: " . $latest->created_at . "\n";
} else {
    echo "No data found. Sync might not be working for Smart Farm.\n";
}
