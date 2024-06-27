<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'company_logo' => '/company_image.png',
            'company_name' => 'Creation Edge',
            'company_email' => 'info@gmail.com',
            'company_phone' => '01711933303',
            'company_address' => 'Uttara #9, Road:3, House: 30, Dhaka-1230',
            'charge_in_dhaka' => '50',
            'charge_out_dhaka' => '100',
            'currency_symbol' => '৳',
        ]);
    }
}
