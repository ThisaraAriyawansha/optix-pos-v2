<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'branch' => 'Colombo Main Branch',
                'name' => 'Lanka Optical Supplies (Pvt) Ltd',
                'email' => 'sales@lankaoptical.lk',
                'phone' => '0112233445',
                'address' => '34 Union Place, Colombo 02',
                'nic' => '198512345671',
                'is_active' => true,
            ],
            [
                'branch' => 'Colombo Main Branch',
                'name' => 'VisionPlus Distributors',
                'email' => 'info@visionplus.lk',
                'phone' => '0112998877',
                'address' => '120 Norris Canal Road, Colombo 10',
                'nic' => '199023456782',
                'is_active' => true,
            ],
            [
                'branch' => 'Kandy Branch',
                'name' => 'Hill Country Eyewear Traders',
                'email' => 'contact@hillcountryeyewear.lk',
                'phone' => '0812233445',
                'address' => '67 Katugastota Road, Kandy',
                'nic' => '197834567893',
                'is_active' => true,
            ],
            [
                'branch' => 'Negombo Branch',
                'name' => 'Coastal Frames & Lenses',
                'email' => null,
                'phone' => '0312234567',
                'address' => '12 Poruthota Road, Negombo',
                'nic' => '199145678904',
                'is_active' => true,
            ],
            [
                'branch' => 'Galle Branch',
                'name' => 'Southern Optics Wholesale',
                'email' => 'orders@southernoptics.lk',
                'phone' => '0912244556',
                'address' => '9 Wakwella Road, Galle',
                'nic' => '198856789015',
                'is_active' => false,
            ],
        ];

        foreach ($suppliers as $supplier) {
            $branch = Branch::where('name', $supplier['branch'])->first();

            if (! $branch) {
                continue;
            }

            Supplier::create([
                'branch_id' => $branch->id,
                'name' => $supplier['name'],
                'email' => $supplier['email'],
                'phone' => $supplier['phone'],
                'address' => $supplier['address'],
                'nic' => $supplier['nic'],
                'is_active' => $supplier['is_active'],
            ]);
        }
    }
}
