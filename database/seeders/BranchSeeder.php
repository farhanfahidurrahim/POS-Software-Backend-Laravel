<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::create([
            'name' => 'Motijheel',
            'country_id' => '18',
            'division_id' => '3',
            'district_id' => '1',
            'upazila_id' => '148',
            'post_code' => '1341',
            'city' => 'Dhaka',
            'state' => 'Dhaka',
            'address' => 'Dhaka',
            'phone_number' => '01700000000',
            'alternate_number' => '01800000000',
            'email' => 'creation.edge@gmail.com',
            'website' => 'www.creation-edge.com',
        ]);
    }
}
