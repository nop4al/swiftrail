<?php

namespace Database\Seeders;

use App\Models\TrainStop;
use App\Models\Train;
use App\Models\Station;
use Illuminate\Database\Seeder;

class CreateTrainStopsSeeder extends Seeder
{
    public function run(): void
    {
        // Get trains and stations
        $trains = Train::all();
        $stations = Station::all();

        if ($trains->isEmpty() || $stations->isEmpty()) {
            echo "Trains or Stations not found. Please seed them first.\n";
            return;
        }

        // Common route: Jakarta (Gambir) -> Surabaya (Pasar Turi)
        // Intermediate: Cirebon -> Pekalongan -> Semarang -> Bojonegoro
        
        // Train 1: AB-5001 (Argo Bromo Ekspres) - Eksekutif - Pagi Siang
        $train1 = $trains->firstWhere('code', 'AB-5001');
        if ($train1) {
            TrainStop::where('train_id', $train1->id)->delete();
            
            $stationSequence = [
                ['name' => 'Gambir', 'arrival' => null, 'departure' => '14:30'],
                ['name' => 'Cirebon', 'arrival' => '16:45', 'departure' => '17:00'],
                ['name' => 'Pekalongan', 'arrival' => '17:45', 'departure' => '18:00'],
                ['name' => 'Semarang Tawang', 'arrival' => '18:45', 'departure' => '19:00'],
                ['name' => 'Bojonegoro', 'arrival' => '20:30', 'departure' => '20:45'],
                ['name' => 'Surabaya Pasar Turi', 'arrival' => '22:00', 'departure' => null],
            ];

            foreach ($stationSequence as $seq => $stationData) {
                $station = $stations->firstWhere('name', $stationData['name']);
                if ($station) {
                    TrainStop::create([
                        'train_id' => $train1->id,
                        'station_id' => $station->id,
                        'sequence' => $seq + 1,
                        'arrival_time' => $stationData['arrival'],
                        'departure_time' => $stationData['departure'],
                    ]);
                }
            }
        }

        // Train 2: EX-3002 (Ekspres Utama Timur) - Bisnis - Pagi
        $train2 = $trains->firstWhere('code', 'EX-3002');
        if ($train2) {
            TrainStop::where('train_id', $train2->id)->delete();
            
            $stationSequence = [
                ['name' => 'Gambir', 'arrival' => null, 'departure' => '06:00'],
                ['name' => 'Cirebon', 'arrival' => '07:30', 'departure' => '07:45'],
                ['name' => 'Pekalongan', 'arrival' => '08:30', 'departure' => '08:45'],
                ['name' => 'Semarang Tawang', 'arrival' => '09:45', 'departure' => '10:00'],
                ['name' => 'Bojonegoro', 'arrival' => '11:30', 'departure' => '11:45'],
                ['name' => 'Surabaya Pasar Turi', 'arrival' => '13:00', 'departure' => null],
            ];

            foreach ($stationSequence as $seq => $stationData) {
                $station = $stations->firstWhere('name', $stationData['name']);
                if ($station) {
                    TrainStop::create([
                        'train_id' => $train2->id,
                        'station_id' => $station->id,
                        'sequence' => $seq + 1,
                        'arrival_time' => $stationData['arrival'],
                        'departure_time' => $stationData['departure'],
                    ]);
                }
            }
        }

        // Train 3: GA-2003 (Gajayana Regional) - Ekonomi - Sore
        $train3 = $trains->firstWhere('code', 'GA-2003');
        if ($train3) {
            TrainStop::where('train_id', $train3->id)->delete();
            
            $stationSequence = [
                ['name' => 'Gambir', 'arrival' => null, 'departure' => '15:30'],
                ['name' => 'Cirebon', 'arrival' => '17:30', 'departure' => '17:45'],
                ['name' => 'Pekalongan', 'arrival' => '18:30', 'departure' => '18:45'],
                ['name' => 'Semarang Tawang', 'arrival' => '19:30', 'departure' => '19:45'],
                ['name' => 'Bojonegoro', 'arrival' => '21:15', 'departure' => '21:30'],
                ['name' => 'Surabaya Pasar Turi', 'arrival' => '23:00', 'departure' => null],
            ];

            foreach ($stationSequence as $seq => $stationData) {
                $station = $stations->firstWhere('name', $stationData['name']);
                if ($station) {
                    TrainStop::create([
                        'train_id' => $train3->id,
                        'station_id' => $station->id,
                        'sequence' => $seq + 1,
                        'arrival_time' => $stationData['arrival'],
                        'departure_time' => $stationData['departure'],
                    ]);
                }
            }
        }

        echo "Train stops created successfully for all trains!\n";
    }
}
