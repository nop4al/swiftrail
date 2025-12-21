<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Train;
use App\Models\TrainStop;

// Check train EX-3002
$train = Train::where('code', 'EX-3002')->first();
echo "Train: " . ($train ? $train->name : 'NOT FOUND') . "\n";

if ($train) {
    $stops = TrainStop::where('train_id', $train->id)
        ->with('station')
        ->orderBy('sequence')
        ->get();
    
    echo "Total stops: " . count($stops) . "\n";
    
    foreach ($stops as $stop) {
        echo "Seq " . $stop->sequence . ": " . $stop->station->name . " (LAT: " . $stop->station->latitude . ", LNG: " . $stop->station->longitude . ")\n";
    }
}
