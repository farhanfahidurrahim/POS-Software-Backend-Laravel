<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shift::create([
            'name' => 'Day',
            'start_time' => '10:00 AM',
            'end_time' => '07:00 AM',
        ]);
        Shift::create([
            'name' => 'Night',
            'start_time' => '08:00 AM',
            'end_time' => '06:00 AM',
        ]);
    }
}
