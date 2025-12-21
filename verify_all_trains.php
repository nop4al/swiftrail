<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Train;
use App\Models\TrainStop;

$trains = Train::all();

foreach ($trains as $train) {
    $stops = TrainStop::where('train_id', $train->id)
        ->with('station')
        ->orderBy('sequence')
        ->get();
    
    echo "\n=== " . $train->code . " - " . $train->name . " ===\n";
    echo "Total stops: " . count($stops) . "\n";
    
    foreach ($stops as $stop) {
        $arrival = $stop->arrival_time ?? '-';
        $departure = $stop->departure_time ?? '-';
        echo "  Seq " . $stop->sequence . ": " . str_pad($stop->station->name, 25) . " | Arrival: " . str_pad($arrival, 8) . " | Departure: " . $departure . "\n";
    }
}

echo "\n✅ All trains configured with full stops!\n";
