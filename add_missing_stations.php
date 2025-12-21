<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Station;

$stations = [
    ['name' => 'Pekalongan', 'code' => 'PKL', 'city' => 'Pekalongan', 'latitude' => -6.8900000, 'longitude' => 109.6640000],
    ['name' => 'Semarang Tawang', 'code' => 'SMG', 'city' => 'Semarang', 'latitude' => -6.9644400, 'longitude' => 110.4277800],
    ['name' => 'Bojonegoro', 'code' => 'BJN', 'city' => 'Bojonegoro', 'latitude' => -7.1639889, 'longitude' => 111.8866639],
];

foreach ($stations as $data) {
    Station::updateOrCreate(
        ['name' => $data['name']],
        $data
    );
    echo "Created/Updated: " . $data['name'] . "\n";
}

echo "\nAll stations created!\n";
