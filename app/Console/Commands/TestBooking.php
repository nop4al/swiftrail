<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class TestBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('=== DATABASE TEST ===');
        $allBookings = Booking::count();
        $this->line("Total Bookings: " . $allBookings);

        // Check for bookings without schedules
        $this->line("\n=== Checking for broken relationships ===");
        
        $bookingsWithoutSchedules = Booking::whereDoesntHave('schedule')->count();
        $this->line("Bookings without schedules: " . $bookingsWithoutSchedules);
        
        // Check schedules without routes
        $scheduleCount = \App\Models\Schedule::count();
        $this->line("Total Schedules: " . $scheduleCount);
        
        $schedulesWithoutRoutes = \App\Models\Schedule::whereDoesntHave('route')->count();
        $this->line("Schedules without routes: " . $schedulesWithoutRoutes);
        
        // Check routes
        $routeCount = \App\Models\Route::count();
        $this->line("Total Routes: " . $routeCount);

        // Now check all bookings with full relations
        $this->line("\n=== ALL BOOKINGS WITH RELATIONS ===");
        $bookings = Booking::with(['schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation', 'user'])->get();
        
        foreach ($bookings as $booking) {
            $this->line("\nBooking ID: " . $booking->id . " | User: " . ($booking->user ? $booking->user->name : 'NO USER') . " | Ticket: " . $booking->ticket_number);
            if (!$booking->schedule) {
                $this->line("  ⚠️  NO SCHEDULE!");
                continue;
            }
            
            $schedule = $booking->schedule;
            if (!$schedule->train) {
                $this->line("  ⚠️  NO TRAIN!");
            } else {
                $this->line("  Train: " . $schedule->train->name);
            }
            
            if (!$schedule->route) {
                $this->line("  ⚠️  NO ROUTE!");
                continue;
            }
            
            $route = $schedule->route;
            $fromName = ($route && $route->departureStation) ? $route->departureStation->name : 'MISSING';
            $toName = ($route && $route->arrivalStation) ? $route->arrivalStation->name : 'MISSING';
            $this->line("  Route: " . $route->code . " | From: " . $fromName . " | To: " . $toName);
        }
    }
}
