<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'name' => 'Opy',
            'email' => 'opy@gmail.com',
            'phone_number' => '01365354541',
            'location' => 'H:32, Road:5, Uttara, Dhaka',
            // 'city_id' => '42',
            // 'city_name' => 'Kishoreganj',
            // 'zone_id' => '144',
            // 'zone_name' => 'Kishoreganj Sadar',
            // 'area_id' => '14096',
            // 'area_name' => 'hasmot school',
        ]);
        Customer::create([
            'name' => 'Fahidur Rahim Farhan',
            'email' => 'fahidurrahim@gmail.com',
            'phone_number' => '01675717825',
            'location' => 'H:32, Road:5, Uttara, Dhaka',
            // 'city_id' => '42',
            // 'city_name' => 'Kishoreganj',
            // 'zone_id' => '144',
            // 'zone_name' => 'Kishoreganj Sadar',
            // 'area_id' => '14096',
            // 'area_name' => 'hasmot school',
        ]);
        Customer::create([
            'name' => 'Tipu',
            'email' => 'tipu@gmail.com',
            'phone_number' => '01389354541',
            'location' => 'H:32, Road:5, Uttara, Dhaka',
            // 'city_id' => '42',
            // 'city_name' => 'Kishoreganj',
            // 'zone_id' => '144',
            // 'zone_name' => 'Kishoreganj Sadar',
            // 'area_id' => '14096',
            // 'area_name' => 'hasmot school',
        ]);
        Customer::create([
            'name' => 'Hasan',
            'email' => 'Hasan@gmail.com',
            'phone_number' => '01365414541',
            'location' => 'H:32, Road:5, Uttara, Dhaka',
            // 'city_id' => '42',
            // 'city_name' => 'Kishoreganj',
            // 'zone_id' => '144',
            // 'zone_name' => 'Kishoreganj Sadar',
            // 'area_id' => '14096',
            // 'area_name' => 'hasmot school',
        ]);
        Customer::create([
            'name' => 'Karim',
            'email' => 'karim@gmail.com',
            'phone_number' => '013893854541',
            'location' => 'H:32, Road:5, Uttara, Dhaka',
            // 'city_id' => '42',
            // 'city_name' => 'Kishoreganj',
            // 'zone_id' => '144',
            // 'zone_name' => 'Kishoreganj Sadar',
            // 'area_id' => '14096',
            // 'area_name' => 'hasmot school',
        ]);
    }
}