<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;

class TestBookingAPI extends Command
{
    protected $signature = 'app:test-booking-api';
    protected $description = 'Test BookingController API response for all users';

    public function handle()
    {
        // Test for each user
        $users = User::all();
        
        foreach ($users as $user) {
            $this->line("\n=== USER: {$user->email} (ID: {$user->id}) ===");
            
            // Manually run the BookingController logic for this user
            $bookings = Booking::where('user_id', $user->id)
                ->with(['schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    $schedule = $booking->schedule;
                    $train = $schedule->train ?? null;
                    $route = $schedule->route ?? null;
                    
                    // Fallback values if relations don't exist
                    $fromStation = ($route && $route->departureStation) ? $route->departureStation->name : 'Unknown';
                    $toStation = ($route && $route->arrivalStation) ? $route->arrivalStation->name : 'Unknown';
                    $trainName = $train ? $train->name : 'Unknown Train';
                    $trainCode = $train ? $train->code : 'UNKNOWN';
                    
                    return [
                        'id' => $booking->id,
                        'from' => $fromStation,
                        'to' => $toStation,
                        'trainName' => $trainName,
                        'trainCode' => $trainCode,
                    ];
                });

            if ($bookings->count() === 0) {
                $this->line("No bookings for this user");
            } else {
                $this->line(json_encode($bookings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }
    }
}

