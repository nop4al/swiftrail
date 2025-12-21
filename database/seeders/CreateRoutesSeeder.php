<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;

class CreateRoutesSeeder extends Seeder
{
    public function run(): void
    {
        // Jakarta (Gambir ID:1) -> Surabaya (Pasar Turi ID:6)
        Route::updateOrCreate(
            ['code' => 'GMB-SBY'],
            [
                'departure_station_id' => 1,
                'arrival_station_id' => 6,
                'distance' => 780,
                'duration' => '6 jam',
                'base_price' => 480000,
                'status' => 'active'
            ]
        );
    }
}
