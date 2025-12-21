<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class CreateSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        // All schedules for route GMB-SBY (Gambir to Surabaya)
        // Route ID 1 (based on CreateRoutesSeeder)
        
        // Train 1: AB-5001 (Argo Bromo Ekspres) - Afternoon
        Schedule::updateOrCreate(
            ['train_id' => 1, 'route_id' => 1, 'departure_time' => '14:30'],
            ['arrival_time' => '22:00', 'days' => '', 'status' => 'active']
        );
        
        // Train 2: EX-3002 (Ekspres Utama Timur) - Morning
        Schedule::updateOrCreate(
            ['train_id' => 2, 'route_id' => 1, 'departure_time' => '06:00'],
            ['arrival_time' => '13:00', 'days' => '', 'status' => 'active']
        );
        
        // Train 3: GA-2003 (Gajayana Regional) - Evening
        Schedule::updateOrCreate(
            ['train_id' => 3, 'route_id' => 1, 'departure_time' => '15:30'],
            ['arrival_time' => '23:00', 'days' => '', 'status' => 'active']
        );
    }
}