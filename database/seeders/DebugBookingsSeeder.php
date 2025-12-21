<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Station;
use App\Models\Route;
use App\Models\Train;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DebugBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds with verbose output.
     */
    public function run(): void
    {
        echo "\n=== CHECKING STATIONS ===\n";
        $stations = Station::all();
        echo "Total Stations: " . $stations->count() . "\n";
        foreach ($stations as $station) {
            echo "  - ID: {$station->id}, Name: {$station->name}, Code: {$station->code}\n";
        }

        echo "\n=== CHECKING TRAINS ===\n";
        $trains = Train::all();
        echo "Total Trains: " . $trains->count() . "\n";
        foreach ($trains as $train) {
            echo "  - ID: {$train->id}, Name: {$train->name}, Code: {$train->code}\n";
        }

        echo "\n=== CHECKING ROUTES ===\n";
        $routes = Route::all();
        echo "Total Routes: " . $routes->count() . "\n";
        foreach ($routes as $route) {
            $depStation = Station::find($route->departure_station_id);
            $arrStation = Station::find($route->arrival_station_id);
            echo "  - ID: {$route->id}, Code: {$route->code}\n";
            echo "    Departure: {$depStation?->name} (ID: {$route->departure_station_id})\n";
            echo "    Arrival: {$arrStation?->name} (ID: {$route->arrival_station_id})\n";
        }

        echo "\n=== CHECKING SCHEDULES ===\n";
        $schedules = Schedule::with(['train', 'route'])->get();
        echo "Total Schedules: " . $schedules->count() . "\n";
        foreach ($schedules as $schedule) {
            echo "  - ID: {$schedule->id}, Train: {$schedule->train?->name}, Route: {$schedule->route?->code}\n";
            echo "    Departure: {$schedule->departure_time}, Arrival: {$schedule->arrival_time}\n";
        }

        echo "\n=== CHECKING USERS ===\n";
        $users = User::all();
        echo "Total Users: " . $users->count() . "\n";
        foreach ($users as $user) {
            echo "  - ID: {$user->id}, Email: {$user->email}\n";
        }

        // Create/Update booking
        $user = User::where('email', 'user@example.com')->first();
        $schedule = Schedule::first();

        echo "\n=== CREATING BOOKING ===\n";
        
        if (!$user) {
            echo "ERROR: No user found!\n";
            return;
        }
        
        if (!$schedule) {
            echo "ERROR: No schedule found!\n";
            return;
        }

        echo "User ID: {$user->id}, Schedule ID: {$schedule->id}\n";

        Booking::updateOrCreate(
            ['booking_code' => 'SWR-202501-AB5001'],
            [
                'ticket_number' => 'TKT-BK20251221163051780617663334704',
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'passenger_name' => 'Naufal Alif Prasetya',
                'nik' => '3175081990101001',
                'passenger_type' => 'Dewasa',
                'seat_number' => '01A',
                'class' => 'Eksekutif 1',
                'price' => 450000,
                'qr_code' => 'SWR-202501-AB5001|TKT-BK20251221163051780617663334704|01A|14:30|20-01-2025',
                'status' => 'confirmed'
            ]
        );
        
        echo "✓ Booking created successfully\n";
        
        echo "\n=== VERIFYING BOOKING ===\n";
        $booking = Booking::with(['schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation'])->first();
        
        if ($booking) {
            echo "Booking ID: {$booking->id}\n";
            echo "Booking Code: {$booking->booking_code}\n";
            echo "Schedule ID: {$booking->schedule_id}\n";
            echo "Train: {$booking->schedule?->train?->name}\n";
            echo "Route Code: {$booking->schedule?->route?->code}\n";
            echo "From: {$booking->schedule?->route?->departureStation?->name}\n";
            echo "To: {$booking->schedule?->route?->arrivalStation?->name}\n";
        }
    }
}
