<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::create([
            'first_name' => 'Jhon',
            'last_name' => 'Kobir',
            'image' => '/jon.webp',
            'email' => 'jhonkobir@gmail.com',
            'designation_id' => '1',
            'phone_number' => '01333523552',
            'family_number' => '01832479919',
            'nid' => '6145696189',
            'dob' => '07/03/1999',
            'gender' => 'male',
            'marital_status' => 'single',
            'blood_group' => 'B+',
            'current_address' => 'Dhaka',
            'permanent_address' => 'Kishoregonj',
            'bank_details' => 'bkash = 01632142799',
            'status' => 'active',
        ]);
        Employee::create([
            'first_name' => 'Mithila',
            'last_name' => 'Farzana',
            'image' => '/mithila.webp',
            'email' => 'mithilafarz@gmail.com',
            'designation_id' => '2',
            'phone_number' => '01745723441',
            'family_number' => '01934527973',
            'nid' => '6141456189',
            'dob' => '07/03/1999',
            'marital_status' => 'married',
            'gender' => 'female',
            'blood_group' => 'O+',
            'current_address' => 'Dhaka',
            'permanent_address' => 'Gazipur',
            'bank_details' => 'nagad = 01721142799',
            'status' => 'active',
        ]);
    }
}