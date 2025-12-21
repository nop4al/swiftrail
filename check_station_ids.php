<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Station;

$stations = Station::all();

echo "=== Current Station IDs ===\n";
foreach ($stations as $station) {
    echo "ID " . $station->id . ": " . $station->code . " - " . $station->name . "\n";
}
