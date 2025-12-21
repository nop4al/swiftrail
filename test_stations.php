<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Station;

$neededStations = ['Gambir', 'Pekalongan', 'Semarang Tawang', 'Bojonegoro', 'Surabaya Pasar Turi', 'Cirebon'];

foreach ($neededStations as $name) {
    $station = Station::where('name', $name)->first();
    echo "Station: " . $name . " - " . ($station ? "EXISTS (ID: " . $station->id . ")" : "NOT FOUND") . "\n";
}
