<?php

namespace Database\Seeders;

use App\Models\CourierShipping;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourierShipping::create([
            'name' => 'Pathao',
            'status' => 'active',
        ]);
        CourierShipping::create([
            'name' => 'Sundarban',
            'status' => 'active',
        ]);
        CourierShipping::create([
            'name' => 'Redx',
            'status' => 'active',
        ]);
    }
}
