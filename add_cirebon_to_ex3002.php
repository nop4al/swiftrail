<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Train;
use App\Models\Station;
use App\Models\TrainStop;

// Get Train EX-3002
$train = Train::where('code', 'EX-3002')->first();
$station = Station::where('name', 'Cirebon')->first();

if ($train && $station) {
    // Delete old stops and recreate with Cirebon
    TrainStop::where('train_id', $train->id)->delete();
    
    $stationSequence = [
        ['name' => 'Gambir', 'arrival' => null, 'departure' => '06:00'],
        ['name' => 'Cirebon', 'arrival' => '07:30', 'departure' => '07:45'],
        ['name' => 'Pekalongan', 'arrival' => '08:30', 'departure' => '08:45'],
        ['name' => 'Semarang Tawang', 'arrival' => '09:45', 'departure' => '10:00'],
        ['name' => 'Bojonegoro', 'arrival' => '11:30', 'departure' => '11:45'],
        ['name' => 'Surabaya Pasar Turi', 'arrival' => '13:00', 'departure' => null],
    ];

    $stations = Station::all();
    
    foreach ($stationSequence as $seq => $stationData) {
        $stat = $stations->firstWhere('name', $stationData['name']);
        if ($stat) {
            TrainStop::create([
                'train_id' => $train->id,
                'station_id' => $stat->id,
                'sequence' => $seq + 1,
                'arrival_time' => $stationData['arrival'],
                'departure_time' => $stationData['departure'],
            ]);
            echo "Added: Seq " . ($seq + 1) . " - " . $stationData['name'] . "\n";
        }
    }
    
    echo "\nEX-3002 updated with Cirebon!\n";
} else {
    echo "Train or Station not found!\n";
}
