<?php

namespace Database\Seeders;

use App\Models\WorkActivity;
use Illuminate\Database\Seeder;

class WorkActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Making',
                'cost_group' => 'labour',
                'is_production' => true,
                'is_dispatch' => false,
                'default_rate' => 3,
                'description' => 'Producing / crushing the product',
            ],
            [
                'name' => 'Sorting & Transport',
                'cost_group' => 'handling',
                'is_production' => false,
                'is_dispatch' => false,
                'default_rate' => 5,
                'description' => 'Picking, sorting and moving to the stock yard',
            ],
            [
                'name' => 'Loading',
                'cost_group' => 'handling',
                'is_production' => false,
                'is_dispatch' => true,
                'default_rate' => 5,
                'description' => 'Loading onto customer vehicles',
            ],
        ];

        foreach ($activities as $activity) {
            WorkActivity::firstOrCreate(['name' => $activity['name']], $activity);
        }
    }
}
