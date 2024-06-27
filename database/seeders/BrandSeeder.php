<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'category_id' => '1',
            'name' => 'Creation Edge',
            'description' => 'This is creation edge.',
            'created_by' => '1',
        ]);

        // Brand::create([
        //     'category_id' => '3',
        //     'name' => 'Infinity',
        //     'description' => 'This is inifinity.',
        //     'created_by' => '1',
        // ]);

        // Brand::create([
        //     'category_id' => '2',
        //     'name' => 'Sara',
        //     'description' => 'This is sara.',
        //     'created_by' => '1',
        // ]);
        // Brand::create([
        //     'category_id' => '1',
        //     'name' => 'Aarong',
        //     'description' => 'This is aarong.',
        //     'created_by' => '1',
        // ]);
        // Brand::create([
        //     'category_id' => '1',
        //     'name' => 'Kay Kraft',
        //     'description' => 'This is kay kraft.',
        //     'created_by' => '1',
        // ]);
        // Brand::create([
        //     'category_id' => '2',
        //     'name' => 'Sadakalo',
        //     'description' => 'This is sadakalo.',
        //     'created_by' => '1',
        // ]);
        // Brand::create([
        //     'category_id' => '3',
        //     'name' => 'Alvira',
        //     'description' => 'This is alvira.',
        //     'created_by' => '1',
        // ]);
        // Brand::create([
        //     'category_id' => '2',
        //     'name' => 'Klothen',
        //     'description' => 'This is klothen.',
        //     'created_by' => '1',
        // ]);
        // Brand::create([
        //     'category_id' => '1',
        //     'name' => 'Diens',
        //     'description' => 'This is diens.',
        //     'created_by' => '1',
        // ]);
    }
}
