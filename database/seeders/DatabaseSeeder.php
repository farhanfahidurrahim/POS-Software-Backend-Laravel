<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // CountrySeeder::class,
            // DivisionSeeder::class,
            // DistrictSeeder::class,
            // UpazilaSeeder::class,
            RolePermissionSeeder::class,
            UserTableSeeder::class,
            BranchSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            UnitSeeder::class,
            ExpenseCategorySeeder::class,
            VariationTemplateSeeder::class,
            VariationValueTemplateSeeder::class,
            ProductSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            EmployeeSeeder::class,
            ShiftSeeder::class,
            LeaveSeeder::class,
            AccountSeeder::class,
            CourierSeeder::class,
            // SaleSeeder::class,
            SettingSeeder::class,
            //PurchaseSeeder::class,
        ]);
    }
}