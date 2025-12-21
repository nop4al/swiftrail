<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;

class TestRefundAPI extends Command
{
    protected $signature = 'app:test-refund-api';
    protected $description = 'Test RefundController getUserTickets endpoint';

    public function handle()
    {
        $this->line('=== TESTING REFUND API - getUserTickets ===\n');
        
        $users = User::all();
        
        foreach ($users as $user) {
            $this->line("User: {$user->email}");
            
            // Simulate the RefundController getUserTickets logic
            $tickets = Booking::where('user_id', $user->id)
                ->with(['schedule.route.departureStation', 'schedule.route.arrivalStation', 'schedule.train'])
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    $route = $booking->schedule->route;
                    return [
                        'id' => $booking->id,
                        'code' => $booking->ticket_number,
                        'trainName' => $booking->schedule->train->name ?? 'Unknown',
                        'from' => ($route && $route->departureStation) ? $route->departureStation->name : 'Unknown',
                        'to' => ($route && $route->arrivalStation) ? $route->arrivalStation->name : 'Unknown',
                        'price' => $booking->price,
                        'departureDate' => $booking->schedule->departure_time,
                        'status' => $booking->status
                    ];
                });

            $this->line(json_encode($tickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->line('---');
        }
    }
}
