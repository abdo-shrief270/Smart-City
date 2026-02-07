<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firebase = app(\App\Services\FirebaseService::class);

echo "Fetching 'smart_farm' (underscore) data from Firebase...\n";
$data = $firebase->get('smart_farm');

if ($data) {
    echo "Data Found:\n";
    var_dump($data);
} else {
    echo "NO DATA found for 'smart_farm'.\n";
}
