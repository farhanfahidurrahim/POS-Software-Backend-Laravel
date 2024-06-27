<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Salwar Kameez',
            'slug' => 'salwar-kameez',
            // 'parent_id' => '',
            'description' => 'This is Salwar Kameez Category.',
        ]);
        Category::create([
            'name' => 'Saree',
            'slug' => 'saree',
            // 'parent_id' => 4,
            'description' => 'This is saree Category.',
        ]);
        // Category::create([
        //     'name' => 'Lehenga',
        //     'slug' => 'lehenga',
        //     // 'parent_id' => '',
        //     'description' => 'This is lehenga Category.',
        // ]);
    }
}
