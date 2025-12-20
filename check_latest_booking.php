<?php

use App\Models\Booking;

$booking = Booking::with('schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation')
    ->latest()
    ->first();

if ($booking) {
    $schedule = $booking->schedule;
    echo "Booking: " . $booking->booking_code . "\n";
    echo "Schedule ID: " . $schedule->id . "\n";
    echo "Train: " . $schedule->train->name . "\n";
    echo "Departure Time: " . $schedule->departure_time . "\n";
    echo "Arrival Time: " . $schedule->arrival_time . "\n";
    echo "From: " . $schedule->route->departureStation->name . "\n";
    echo "To: " . $schedule->route->arrivalStation->name . "\n";
    echo "Duration: " . $schedule->route->duration . "\n";
} else {
    echo "No bookings found";
}
