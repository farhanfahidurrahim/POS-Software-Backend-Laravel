<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ExpenseCategory::create([
            'parent_id' => '1',
            'name' => 'Rent',
            'code' => 'Rent',
        ]);
        ExpenseCategory::create([
            'parent_id' => '2',
            'name' => 'Electricity',
            'code' => 'Electricity',
        ]);
        ExpenseCategory::create([
            'parent_id' => '3',
            'name' => 'Water',
            'code' => 'Water',
        ]);
        ExpenseCategory::create([
            'parent_id' => '4',
            'name' => 'Internet',
            'code' => 'Internet',
        ]);
        ExpenseCategory::create([
            'parent_id' => '5',
            'name' => 'Maintenance',
            'code' => 'Maintenance',
        ]);
        ExpenseCategory::create([
            'parent_id' => '6',
            'name' => 'Tea',
            'code' => 'Tea',
        ]);
    }
}
