<?php

namespace Database\Seeders;

use App\Models\Leave;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Leave::create([
            'name' => 'Sick',
            'leave_count' => '2',
        ]);
        Leave::create([
            'name' => 'Mental illness',
            'leave_count' => '2',
        ]);
    }
}
