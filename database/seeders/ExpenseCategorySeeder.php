<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Shop Rent', 'description' => 'Monthly rent for the shop premises', 'color' => 'indigo', 'icon' => 'home'],
            ['name' => 'Electricity', 'description' => 'CEB / LECO electricity bills', 'color' => 'amber', 'icon' => 'bolt'],
            ['name' => 'Water', 'description' => 'Water board bills', 'color' => 'blue', 'icon' => 'droplet'],
            ['name' => 'Internet & Phone', 'description' => 'Broadband, mobile and landline bills', 'color' => 'teal', 'icon' => 'wifi'],
            ['name' => 'Staff Salaries', 'description' => 'Salaries, wages and staff advances', 'color' => 'green', 'icon' => 'users'],
            ['name' => 'Transport', 'description' => 'Fuel, delivery and courier charges', 'color' => 'orange', 'icon' => 'truck'],
            ['name' => 'Repairs & Maintenance', 'description' => 'Equipment and shop repairs', 'color' => 'red', 'icon' => 'wrench'],
            ['name' => 'Shop Supplies', 'description' => 'Stationery, bags and packing material', 'color' => 'purple', 'icon' => 'cart'],
            ['name' => 'Marketing', 'description' => 'Advertising, banners and promotions', 'color' => 'pink', 'icon' => 'megaphone'],
            ['name' => 'Tea & Refreshments', 'description' => 'Tea, snacks and staff meals', 'color' => 'orange', 'icon' => 'coffee'],
            ['name' => 'Cleaning', 'description' => 'Cleaning services and materials', 'color' => 'teal', 'icon' => 'sparkles'],
            ['name' => 'Taxes & Licenses', 'description' => 'Business registration, licenses and taxes', 'color' => 'gray', 'icon' => 'document'],
            ['name' => 'Miscellaneous', 'description' => 'Other day-to-day expenses', 'color' => 'gray', 'icon' => 'dots'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
