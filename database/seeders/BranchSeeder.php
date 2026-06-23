<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Colombo Main Branch',
                'description' => 'Flagship store and head office for OptiX POS.',
                'address' => 'No. 12, Galle Road, Colombo 03',
                'main_contact' => '0112345678',
                'secondary_contact' => '0771234567',
                'status' => true,
            ],
            [
                'name' => 'Kandy Branch',
                'description' => 'Serves the central province with a full eyewear catalog.',
                'address' => '45 Peradeniya Road, Kandy',
                'main_contact' => '0812234455',
                'secondary_contact' => null,
                'status' => true,
            ],
            [
                'name' => 'Negombo Branch',
                'description' => 'Coastal branch focused on walk-in retail sales.',
                'address' => '7 Lewis Place, Negombo',
                'main_contact' => '0312456789',
                'secondary_contact' => '0775566778',
                'status' => true,
            ],
            [
                'name' => 'Galle Branch',
                'description' => 'Southern province branch with on-site repair services.',
                'address' => '23 Matara Road, Galle',
                'main_contact' => '0912233445',
                'secondary_contact' => null,
                'status' => true,
            ],
            [
                'name' => 'Jaffna Branch',
                'description' => 'Northern province branch, currently closed for renovation.',
                'address' => '18 Hospital Road, Jaffna',
                'main_contact' => '0212223344',
                'secondary_contact' => null,
                'status' => false,
            ],
            [
                'name' => 'Kurunegala Pop-up',
                'description' => 'Seasonal pop-up branch, opens during festival periods only.',
                'address' => '5 Kandy Road, Kurunegala',
                'main_contact' => '0372234567',
                'secondary_contact' => '0712345678',
                'status' => false,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
