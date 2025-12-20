<?php
// Simple script to update all schedules to have Mon,Tue,Wed,Thu,Fri,Sat,Sun format

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Schedule;

$allDays = 'Mon,Tue,Wed,Thu,Fri,Sat,Sun';

// Update all active schedules
$updated = Schedule::where('status', 'active')
    ->where('days', '!=', $allDays)
    ->update(['days' => $allDays]);

echo "Updated $updated schedules to operate on all days.\n";

// Show updated schedules
$schedules = Schedule::where('status', 'active')->get();
foreach ($schedules as $schedule) {
    echo "Schedule {$schedule->id}: {$schedule->departure_time} - {$schedule->arrival_time} (Days: {$schedule->days})\n";
}
