<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create([
            'name' => 'Piece',
            'short_name' => 'pcs',
            'base_unit_id' => Null,
            // 'allow_decimal' => '0',
            'base_unit_multiplier' => Null,
            'user_id' => '1',
        ]);
    }
}
