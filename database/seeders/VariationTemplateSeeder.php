<?php

namespace Database\Seeders;

use App\Models\VariationTemplate;
use App\Models\VariationValueTemplate;
use Illuminate\Database\Seeder;

class VariationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VariationTemplate::create([
            'name' => 'SalwarKameez',
        ]);
        VariationTemplate::create([
            'name' => 'Saree',
        ]);

        // VariationTemplate::create([
        //     'name' => 'Borka',
        // ]);
    }
}
