<?php

use App\Models\Booking;

Booking::where('status', 'pending')->update(['status' => 'confirmed']);
$count = Booking::where('status', 'confirmed')->count();
echo "✓ All pending bookings updated to confirmed. Total confirmed: $count";
