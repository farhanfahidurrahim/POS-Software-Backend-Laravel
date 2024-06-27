<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::create([
            'name' => 'Rohit Shetty',
            'email' => 'rohitshetty@gmail.com',
            'phone_number' => '01937564157',
            'company_name' => 'Classic_it',
            'company_number' => '01777855562',
            'license_number' => '86556535857655498',
            'location' => 'Dhaka',
        ]);

        Supplier::create([
            'name' => 'Karim',
            'email' => 'karim@gmail.com',
            'phone_number' => '01665855555',
            'company_name' => 'Classic_it',
            'company_number' => '01997855551',
            'license_number' => '21556535857655482',
            'location' => 'Dhaka',
        ]);

        Supplier::create([
            'name' => 'Hasim',
            'email' => 'hasim@gmail.com',
            'phone_number' => '01555855555',
            'company_name' => 'Classic_it',
            'company_number' => '01777855555',
            'license_number' => '68556535857655489',
            'location' => 'Dhaka',
        ]);

        Supplier::create([
            'name' => 'Mukkar',
            'email' => 'mukkar@gmail.com',
            'phone_number' => '01555744555',
            'company_name' => 'Classic_it',
            'company_number' => '01888744555',
            'license_number' => '5347531435553554',
            'location' => 'Dhaka',
        ]);
    }
}
