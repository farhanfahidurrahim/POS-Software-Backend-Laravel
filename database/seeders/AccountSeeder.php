<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::create([
            'available_balance' => '500000.00',
        ]);

        // Account::create([
        //     'name' => 'Bkash',
        //     'account_number' => '01000000000',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);

        // Account::create([
        //     'name' => 'Nagad',
        //     'account_number' => '01200000000',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);

        // Account::create([
        //     'name' => 'Rocket',
        //     'account_number' => '01300000000',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);

        // Account::create([
        //     'name' => 'Cash',
        //     'account_number' => '001',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);

        // Account::create([
        //     'name' => 'Bkash',
        //     'account_number' => '01000000000',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);

        // Account::create([
        //     'name' => 'Nagad',
        //     'account_number' => '01200000000',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);

        // Account::create([
        //     'name' => 'Rocket',
        //     'account_number' => '01300000000',
        //     'available_balance' => '0.00',
        //     'status' => 'active'
        // ]);
    }
}
