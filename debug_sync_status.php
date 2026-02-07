<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Monitoring Smart Tank Data (Press Ctrl+C to stop)...\n";
echo "Current Time: " . now()->toDateTimeString() . "\n";

$lastCount = 0;

for ($i = 0; $i < 5; $i++) {
    $count = \App\Models\SmartTankData::count();
    $latest = \App\Models\SmartTankData::latest()->first();

    echo "[$i] Total Records: $count";
    if ($latest) {
        echo " | Latest: ID={$latest->id} Level={$latest->level} Status={$latest->status} Pump=" . ($latest->is_pump_on ? 'ON' : 'OFF') . " Time={$latest->created_at}";
    }
    echo "\n";

    if ($count > $lastCount && $lastCount !== 0) {
        echo "   -> NEW RECORD DETECTED!\n";
    }

    $lastCount = $count;
    sleep(2);
}
