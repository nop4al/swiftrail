<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class CreateStationsSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            // Jakarta
            ['code' => 'GMB', 'name' => 'Gambir', 'city' => 'Jakarta', 'latitude' => -6.1744, 'longitude' => 106.8294],
            
            // Cirebon
            ['code' => 'CRB', 'name' => 'Cirebon', 'city' => 'Cirebon', 'latitude' => -6.7034, 'longitude' => 108.4689],
            
            // Pekalongan
            ['code' => 'PKL', 'name' => 'Pekalongan', 'city' => 'Pekalongan', 'latitude' => -6.89, 'longitude' => 109.664],
            
            // Semarang
            ['code' => 'SMG', 'name' => 'Semarang Tawang', 'city' => 'Semarang', 'latitude' => -6.96444, 'longitude' => 110.42778],
            
            // Bojonegoro
            ['code' => 'BJN', 'name' => 'Bojonegoro', 'city' => 'Bojonegoro', 'latitude' => -7.1639889, 'longitude' => 111.8866639],
            
            // Surabaya
            ['code' => 'SBY-P', 'name' => 'Surabaya Pasar Turi', 'city' => 'Surabaya', 'latitude' => -7.2575, 'longitude' => 112.7521],
        ];

        foreach ($stations as $station) {
            Station::updateOrCreate(
                ['name' => $station['name']],
                $station
            );
        }
    }
}
