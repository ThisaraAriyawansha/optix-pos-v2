<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'branch' => 'Colombo Main Branch',
                'name' => 'Nimal Perera',
                'email' => 'nimal.perera@gmail.com',
                'phone' => '0771234567',
                'is_active' => true,
            ],
            [
                'branch' => 'Colombo Main Branch',
                'name' => 'Kasun Fernando',
                'email' => null,
                'phone' => '0772345678',
                'is_active' => true,
            ],
            [
                'branch' => 'Kandy Branch',
                'name' => 'Dilani Senanayake',
                'email' => 'dilani.s@gmail.com',
                'phone' => '0813345678',
                'is_active' => true,
            ],
            [
                'branch' => 'Negombo Branch',
                'name' => 'Ruwan Jayasuriya',
                'email' => 'ruwan.j@gmail.com',
                'phone' => '0314456789',
                'is_active' => true,
            ],
            [
                'branch' => 'Galle Branch',
                'name' => 'Chamari Wickramasinghe',
                'email' => null,
                'phone' => '0915567890',
                'is_active' => false,
            ],
        ];

        foreach ($customers as $customer) {
            $branch = Branch::where('name', $customer['branch'])->first();

            if (! $branch) {
                continue;
            }

            $lastId = Customer::max('id') ?? 0;

            Customer::create([
                'customer_code' => 'CUS-'.str_pad((string) ($lastId + 1), 4, '0', STR_PAD_LEFT),
                'branch_id' => $branch->id,
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'is_active' => $customer['is_active'],
            ]);
        }
    }
}
