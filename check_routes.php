<?php

use App\Models\Schedule;
use App\Models\Booking;

echo "=== SCHEDULES ===\n";
$schedules = Schedule::with('train', 'route.departureStation', 'route.arrivalStation')->get();
foreach($schedules as $s) {
  echo 'Schedule ID: ' . $s->id . ' | Train: ' . $s->train->name . ' | From: ' . $s->route->departureStation->name . ' → To: ' . $s->route->arrivalStation->name . "\n";
}

echo "\n=== BOOKINGS ===\n";
$bookings = Booking::with('schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation')->get();
foreach($bookings as $b) {
  echo 'Booking: ' . $b->booking_code . ' | User: ' . $b->user_id . ' | Train: ' . $b->schedule->train->name . ' | From: ' . $b->schedule->route->departureStation->name . ' → To: ' . $b->schedule->route->arrivalStation->name . "\n";
}
