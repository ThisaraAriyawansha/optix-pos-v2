<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Models\WorkActivity;
use App\Models\WorkEntry;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for the stone quarry: raw materials, products with full cost build-up,
 * workers on every pay type, two weeks of daily work and one week already paid.
 * Safe to run more than once — existing records are reused and work is only added
 * when there is none yet.
 */
class QuarryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(WorkActivitySeeder::class);

        mt_srand(2026);

        DB::transaction(function () {
            $branches = $this->branches();
            $materials = $this->materials();
            $products = $this->products($materials);
            $workers = $this->workers($branches);

            if (WorkEntry::exists()) {
                $this->command?->info('Work entries already exist — skipped demo work and salary.');

                return;
            }

            $this->workEntries($workers, $products);
            $this->salaries($workers);
        });
    }

    protected function branches()
    {
        $branches = Branch::where('status', true)->orderBy('id')->take(2)->get();

        if ($branches->isEmpty()) {
            $branches->push(Branch::create([
                'name' => 'Main Quarry',
                'address' => 'Quarry Road, Kurunegala',
                'main_contact' => '0372200000',
                'status' => true,
            ]));
        }

        return $branches;
    }

    protected function materials(): array
    {
        $materials = [
            'Explosives' => ['unit' => 'kg', 'unit_cost' => 1200, 'description' => 'Blasting explosives'],
            'Detonators' => ['unit' => 'piece', 'unit_cost' => 85, 'description' => 'Electric detonators'],
            'Diesel' => ['unit' => 'litre', 'unit_cost' => 360, 'description' => 'Excavator & loader fuel'],
            'Crusher Electricity' => ['unit' => 'kWh', 'unit_cost' => 55, 'description' => 'Crusher machine power'],
        ];

        return collect($materials)
            ->mapWithKeys(fn ($attributes, $name) => [$name => RawMaterial::firstOrCreate(['name' => $name], $attributes)])
            ->all();
    }

    protected function products(array $materials): array
    {
        $making = WorkActivity::where('name', 'Making')->first();
        $sorting = WorkActivity::where('name', 'Sorting & Transport')->first();
        $loading = WorkActivity::where('name', 'Loading')->first();

        // name => [materials per cube, [making, sorting, loading] rate, commission type, commission, selling price]
        $catalog = [
            '3/4" Metal' => [['Explosives' => 0.4, 'Detonators' => 1, 'Diesel' => 2.5, 'Crusher Electricity' => 12], [450, 150, 200], 'fixed', 250, 12500],
            '1 1/2" Metal' => [['Explosives' => 0.4, 'Detonators' => 1, 'Diesel' => 2.2, 'Crusher Electricity' => 9], [400, 150, 200], 'fixed', 250, 10500],
            '3/8" Chips' => [['Explosives' => 0.4, 'Detonators' => 1, 'Diesel' => 2.5, 'Crusher Electricity' => 14], [500, 150, 200], 'percent', 5, 13500],
            'ABC (Base Course)' => [['Explosives' => 0.3, 'Diesel' => 2, 'Crusher Electricity' => 8], [300, 150, 200], 'fixed', 200, 8000],
            'Quarry Dust' => [['Diesel' => 1.5, 'Crusher Electricity' => 6], [200, 100, 200], 'percent', 5, 6500],
            'Rubble (Boulders)' => [['Explosives' => 0.5, 'Detonators' => 1, 'Diesel' => 1], [350, null, 250], 'fixed', 300, 7000],
        ];

        $products = [];

        foreach ($catalog as $name => [$recipe, $rates, $commissionType, $commission, $price]) {
            $product = Product::firstOrCreate(['name' => $name], [
                'unit' => 'Cube',
                'commission_type' => $commissionType,
                'commission_value' => $commission,
                'selling_price' => $price,
            ]);

            $product->materials()->sync(collect($recipe)->mapWithKeys(fn ($qty, $material) => [$materials[$material]->id => ['quantity' => $qty]])->all());
            $product->activityRates()->sync(collect([$making->id => $rates[0], $sorting->id => $rates[1], $loading->id => $rates[2]])
                ->filter(fn ($rate) => $rate !== null)
                ->map(fn ($rate) => ['rate' => $rate])
                ->all());
            $product->recalculateCosts();

            $products[] = $product;
        }

        return $products;
    }

    protected function workers($branches): array
    {
        $main = $branches->first()->id;
        $second = $branches->last()->id;

        $people = [
            ['Sunil Perera', 'piece_rate', $main],
            ['Nimal Bandara', 'piece_rate', $main],
            ['Kamal Rathnayake', 'piece_rate', $main],
            ['Saman Kumara', 'piece_rate', $main],
            ['Ajith Wijesinghe', 'piece_rate', $main],
            ['Chaminda Silva', 'piece_rate', $second],
            ['Upali Herath', 'piece_rate', $second],
            ['Gamini Dissanayake', 'daily', $main, 2500],
            ['Ranjith Fernando', 'daily', $main, 2500],
            ['Priyantha Jayasena', 'daily', $second, 2300],
            ['Lalith Gunawardena', 'monthly', $main, null, 65000, 'Quarry supervisor'],
            ['Dilani Weerasinghe', 'monthly', $main, null, 45000, 'Store keeper & weigh bridge'],
        ];

        $workers = [];

        foreach ($people as $index => $person) {
            [$name, $payType, $branchId] = $person;

            $workers[] = Worker::firstOrCreate(['name' => $name], [
                'nic' => (1975 + $index * 2).str_pad((string) (10000 + $index * 731), 8, '0', STR_PAD_LEFT),
                'phone' => '07'.mt_rand(10000000, 89999999),
                'address' => 'Quarry Road, Village '.($index + 1),
                'branch_id' => $branchId,
                'pay_type' => $payType,
                'daily_rate' => $person[3] ?? null,
                'monthly_salary' => $person[4] ?? null,
                'notes' => $person[5] ?? null,
                'joined_on' => now()->subMonths(mt_rand(3, 36))->toDateString(),
            ]);
        }

        return $workers;
    }

    /**
     * Two weeks of day-end entries (Sundays off). Making quantities run highest so the
     * yard builds stock; loading is what leaves on customer vehicles.
     */
    protected function workEntries(array $workers, array $products): void
    {
        $activities = WorkActivity::pluck('id', 'name');
        $userId = User::orderBy('id')->value('id');

        for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
            $date = today()->subDays($daysAgo);

            if ($date->isSunday()) {
                continue;
            }

            foreach ($workers as $worker) {
                $roll = mt_rand(1, 100);
                $attendance = $roll <= 85 ? 'present' : ($roll <= 93 ? 'half_day' : 'absent');
                $lines = [];

                if ($attendance !== 'absent' && $worker->pay_type !== 'monthly') {
                    $scale = $attendance === 'half_day' ? 0.5 : 1;

                    // array_rand returns a single key when asked for one, so cast to array.
                    foreach ((array) array_rand(array_flip(['Making', 'Sorting & Transport', 'Loading']), mt_rand(1, 3)) as $activity) {
                        $product = $products[array_rand($products)];
                        $rateExists = $product->activityRates->contains('name', $activity);

                        $lines[] = [
                            'product_id' => $product->id,
                            'work_activity_id' => $activities[$rateExists ? $activity : 'Making'],
                            'quantity' => max(1, round(mt_rand(...match ($activity) {
                                'Making' => [4, 12],
                                'Sorting & Transport' => [3, 10],
                                default => [2, 8],
                            }) * $scale)),
                        ];
                    }
                }

                $entry = WorkEntry::create([
                    'worker_id' => $worker->id,
                    'branch_id' => $worker->branch_id,
                    'user_id' => $userId,
                    'work_date' => $date->toDateString(),
                    'attendance' => $attendance,
                ]);
                $entry->syncItems($lines);
            }
        }
    }

    /**
     * Last week's wages are already paid; this week's are still owed.
     */
    protected function salaries(array $workers): void
    {
        $userId = User::orderBy('id')->value('id');
        $from = today()->subDays(13);
        $to = today()->subDays(7);

        foreach ($workers as $worker) {
            if ($worker->pay_type === 'monthly') {
                continue;
            }

            $entries = WorkEntry::where('worker_id', $worker->id)
                ->whereNull('salary_payment_id')
                ->whereBetween('work_date', [$from->toDateString(), $to->toDateString()])
                ->get();

            $earned = (float) $entries->sum('total_earnings');
            $deductions = $worker->id % 4 === 0 ? 1000 : 0; // a salary advance taken mid-week

            if ($earned - $deductions <= 0) {
                continue;
            }

            $payment = SalaryPayment::create([
                'payment_code' => 'SAL-'.str_pad((string) ((SalaryPayment::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT),
                'worker_id' => $worker->id,
                'branch_id' => $worker->branch_id,
                'user_id' => $userId,
                'period_from' => $from->toDateString(),
                'period_to' => $to->toDateString(),
                'work_earnings' => $earned,
                'deductions' => $deductions,
                'net_amount' => $earned - $deductions,
                'payment_method' => 'cash',
                'paid_on' => today()->subDays(6)->toDateString(),
                'notes' => $deductions ? 'Advance of Rs. 1,000 deducted' : null,
            ]);

            WorkEntry::whereIn('id', $entries->pluck('id'))->update(['salary_payment_id' => $payment->id]);
        }
    }
}
