<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'name' => 'Account',
            'description' => 'Account des'
        ]);
        Department::create([
            'name' => 'Developer',
            'description' => 'Developer des'
        ]);
        Department::create([
            'name' => 'Marketing',
            'description' => 'Marketing des'
        ]);
    }
}
