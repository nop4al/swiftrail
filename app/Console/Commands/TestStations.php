<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Station;

class TestStations extends Command
{
    protected $signature = 'app:test-stations';
    protected $description = 'Check all stations';

    public function handle()
    {
        $this->line('=== ALL STATIONS ===');
        
        $stations = Station::all();
        foreach ($stations as $station) {
            $this->line("ID: {$station->id} | Code: {$station->code} | Name: {$station->name} | City: {$station->city}");
        }
    }
}
