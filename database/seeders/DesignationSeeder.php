<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Designation::create([
            'name' => 'Jr. Soft. Eng',
            'description' => 'Eng',
        ]);
        Designation::create([
            'name' => 'Sr. Soft. Eng',
            'description' => 'Eng',
        ]);
    }
}
