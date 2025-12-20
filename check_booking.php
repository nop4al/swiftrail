<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;

$booking = Booking::with(['schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation'])->first();

if ($booking) {
    echo "=== FIRST BOOKING ===\n";
    echo "ID: " . $booking->id . "\n";
    echo "Booking Code: " . $booking->booking_code . "\n";
    echo "QR Code: " . ($booking->qr_code ?? 'NULL') . "\n";
    echo "Passenger: " . $booking->passenger_name . "\n";
    echo "Status: " . $booking->status . "\n";
} else {
    echo "No bookings found\n";
}
?>
