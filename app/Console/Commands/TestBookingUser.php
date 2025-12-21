<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class TestBookingUser extends Command
{
    protected $signature = 'app:test-booking-user';
    protected $description = 'Check booking user_ids';

    public function handle()
    {
        $this->line('=== BOOKING USER IDS ===');
        
        $bookings = Booking::all();
        foreach ($bookings as $booking) {
            $this->line("Booking ID: {$booking->id} | user_id: " . ($booking->user_id ?? 'NULL'));
        }
        
        $this->line("\n=== USERS IN DATABASE ===");
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $this->line("User ID: {$user->id} | Name: {$user->name} | Email: {$user->email}");
        }
    }
}
