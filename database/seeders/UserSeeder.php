<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = UserRole::where('name', 'Admin')->first();
        $managerRole = UserRole::where('name', 'Manager')->first();
        $cashierRole = UserRole::where('name', 'Cashier')->first();

        $colombo = Branch::where('name', 'Colombo Main Branch')->first();
        $kandy = Branch::where('name', 'Kandy Branch')->first();
        $negombo = Branch::where('name', 'Negombo Branch')->first();

        $users = [
            [
                'name' => 'Sahan Fernando',
                'email' => 'sahan.admin@optix.com',
                'phone_number' => '0771112222',
                'address' => 'No. 5, Park Street, Colombo 02',
                'role_id' => $adminRole?->id,
                'branch_id' => null,
                'status' => true,
            ],
            [
                'name' => 'Kavindu Silva',
                'email' => 'kavindu.manager@optix.com',
                'phone_number' => '0772223333',
                'address' => 'No. 12, Galle Road, Colombo 03',
                'role_id' => $managerRole?->id,
                'branch_id' => $colombo?->id,
                'status' => true,
            ],
            [
                'name' => 'Dinithi Perera',
                'email' => 'dinithi.cashier@optix.com',
                'phone_number' => '0773334444',
                'address' => '45 Peradeniya Road, Kandy',
                'role_id' => $cashierRole?->id,
                'branch_id' => $kandy?->id,
                'status' => true,
            ],
            [
                'name' => 'Ruwan Jayasuriya',
                'email' => 'ruwan.cashier@optix.com',
                'phone_number' => '0774445555',
                'address' => '7 Lewis Place, Negombo',
                'role_id' => $cashierRole?->id,
                'branch_id' => $negombo?->id,
                'status' => false,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user + ['password' => 'password123']
            );
        }
    }
}
