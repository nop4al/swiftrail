<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;

// Update all bookings that have null qr_code
$bookings = Booking::whereNull('qr_code')->get();

echo "Found " . count($bookings) . " bookings without QR code\n";

foreach ($bookings as $booking) {
    // Generate QR code data using booking code
    $qrCodeData = $booking->booking_code;
    
    $booking->update(['qr_code' => $qrCodeData]);
    
    echo "Updated booking #{$booking->id} - {$booking->booking_code} with QR code\n";
}

echo "Done! All bookings now have QR codes.\n";
?>
