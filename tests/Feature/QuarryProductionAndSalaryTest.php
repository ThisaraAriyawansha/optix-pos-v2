<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Models\UserRole;
use App\Models\WorkActivity;
use App\Models\WorkEntry;
use App\Models\Worker;
use Database\Seeders\QuarryDemoSeeder;
use Database\Seeders\WorkActivitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuarryProductionAndSalaryTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected User $admin;

    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WorkActivitySeeder::class);

        $this->branch = Branch::create(['name' => 'Quarry 1', 'address' => 'Site', 'main_contact' => '0770000000']);
        $this->admin = User::factory()->create(['role_id' => UserRole::create(['name' => 'Super Admin'])->id, 'branch_id' => $this->branch->id]);
        $this->cashier = User::factory()->create(['role_id' => UserRole::create(['name' => 'Cashier'])->id, 'branch_id' => $this->branch->id]);
    }

    protected function activity(string $name): WorkActivity
    {
        return WorkActivity::where('name', $name)->firstOrFail();
    }

    protected function makeProduct(string $name, array $materials = []): Product
    {
        $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => $name,
            'unit' => 'Cube',
            'materials' => $materials,
            'rates' => [
                $this->activity('Making')->id => 3,
                $this->activity('Sorting & Transport')->id => 5,
                $this->activity('Loading')->id => 5,
            ],
            'commission_type' => 'percent',
            'commission_value' => 10,
            'selling_price' => 100,
        ])->assertRedirect(route('products'));

        return Product::where('name', $name)->firstOrFail();
    }

    public function test_product_cost_is_built_from_materials_labour_handling_and_commission(): void
    {
        $explosive = RawMaterial::create(['name' => 'Explosive', 'unit' => 'kg', 'unit_cost' => 100]);

        $product = $this->makeProduct('3/4 Metal', [['raw_material_id' => $explosive->id, 'quantity' => 0.5]]);

        $this->assertEquals(50, $product->material_cost);
        $this->assertEquals(3, $product->labour_cost);
        $this->assertEquals(10, $product->handling_cost);
        $this->assertEquals(63, $product->total_cost);
        $this->assertEquals(6.30, $product->commission_amount);
        $this->assertEqualsWithDelta(30.70, $product->profit(), 0.001);

        // Changing a material's cost re-prices every product made from it.
        $this->put(route('products.materials.update', $explosive), [
            'name' => 'Explosive', 'unit' => 'kg', 'unit_cost' => 120, 'is_active' => 1,
        ])->assertRedirect(route('products.materials'));

        $this->assertEquals(73, $product->fresh()->total_cost);
    }

    public function test_only_admins_manage_pricing_but_everyone_sees_products(): void
    {
        $product = $this->makeProduct('Quarry Dust');

        $this->actingAs($this->cashier)->get(route('products'))
            ->assertOk()
            ->assertSee('Quarry Dust')
            ->assertDontSee('Total Cost');
        $this->get(route('products.create'))->assertForbidden();
        $this->get(route('products.edit', $product))->assertForbidden();
        $this->get(route('products.materials'))->assertForbidden();

        $this->actingAs($this->admin)->get(route('products'))->assertSee('Total Cost');
    }

    public function test_day_end_work_entry_pays_piece_rate_and_daily_workers(): void
    {
        $a = $this->makeProduct('Product A');
        $b = $this->makeProduct('Product B');
        $c = $this->makeProduct('Product C');

        $pieceWorker = Worker::create(['code' => 'WRK-0001', 'name' => 'Sunil', 'branch_id' => $this->branch->id, 'pay_type' => 'piece_rate']);
        $dailyWorker = Worker::create(['code' => 'WRK-0002', 'name' => 'Nimal', 'branch_id' => $this->branch->id, 'pay_type' => 'daily', 'daily_rate' => 2500]);

        // "Today I sorted 5 of A, loaded 10 of B and made 10 of C."
        $this->actingAs($this->cashier)->post(route('labour.work.store'), [
            'worker_id' => $pieceWorker->id,
            'branch_id' => $this->branch->id,
            'work_date' => today()->toDateString(),
            'attendance' => 'present',
            'items' => [
                ['product_id' => $a->id, 'work_activity_id' => $this->activity('Sorting & Transport')->id, 'quantity' => 5],
                ['product_id' => $b->id, 'work_activity_id' => $this->activity('Loading')->id, 'quantity' => 10],
                ['product_id' => $c->id, 'work_activity_id' => $this->activity('Making')->id, 'quantity' => 10],
            ],
        ])->assertRedirect();

        $entry = WorkEntry::where('worker_id', $pieceWorker->id)->firstOrFail();
        $this->assertEquals(5 * 5 + 10 * 5 + 10 * 3, $entry->total_earnings); // 105

        // Daily-wage worker: paid the day rate, quantities still count toward production.
        $this->post(route('labour.work.store'), [
            'worker_id' => $dailyWorker->id,
            'branch_id' => $this->branch->id,
            'work_date' => today()->toDateString(),
            'attendance' => 'half_day',
            'items' => [['product_id' => $c->id, 'work_activity_id' => $this->activity('Making')->id, 'quantity' => 4]],
        ])->assertRedirect();

        $dailyEntry = WorkEntry::where('worker_id', $dailyWorker->id)->firstOrFail();
        $this->assertEquals(1250, $dailyEntry->total_earnings);
        $this->assertEquals(0, $dailyEntry->items()->sum('amount'));

        // Same worker cannot be entered twice for one day.
        $this->post(route('labour.work.store'), [
            'worker_id' => $pieceWorker->id, 'branch_id' => $this->branch->id,
            'work_date' => today()->toDateString(), 'attendance' => 'present',
        ])->assertSessionHasErrors('worker_id');

        // Production report: C produced 14, B dispatched 10.
        $report = $this->get(route('labour.production'))->assertOk()->viewData('report')->keyBy(fn ($row) => $row['product']->name);
        $this->assertEquals(14, $report['Product C']['produced']);
        $this->assertEquals(10, $report['Product B']['dispatched']);
        $this->assertEquals(-10, $report['Product B']['balance']);
    }

    public function test_paying_salary_settles_unpaid_work_and_locks_it(): void
    {
        $product = $this->makeProduct('Product A');
        $worker = Worker::create(['code' => 'WRK-0001', 'name' => 'Sunil', 'branch_id' => $this->branch->id, 'pay_type' => 'piece_rate']);

        foreach ([today()->subDay(), today()] as $day) {
            $this->actingAs($this->cashier)->post(route('labour.work.store'), [
                'worker_id' => $worker->id,
                'branch_id' => $this->branch->id,
                'work_date' => $day->toDateString(),
                'attendance' => 'present',
                'items' => [['product_id' => $product->id, 'work_activity_id' => $this->activity('Making')->id, 'quantity' => 100]],
            ]);
        }

        $this->post(route('labour.salary.store'), [
            'worker_id' => $worker->id,
            'period_from' => today()->subDay()->toDateString(),
            'period_to' => today()->toDateString(),
            'bonus' => 50,
            'deductions' => 100,
            'payment_method' => 'cash',
            'paid_on' => today()->toDateString(),
        ])->assertRedirect();

        $payment = SalaryPayment::firstOrFail();
        $this->assertEquals(600, $payment->work_earnings);
        $this->assertEquals(550, $payment->net_amount);
        $this->assertEquals(0, WorkEntry::whereNull('salary_payment_id')->count());

        // Paid work can no longer be changed.
        $entry = WorkEntry::first();
        $this->put(route('labour.work.update', $entry), [
            'worker_id' => $worker->id, 'branch_id' => $this->branch->id,
            'work_date' => $entry->work_date->toDateString(), 'attendance' => 'absent',
        ])->assertSessionHas('error');
        $this->assertEquals('present', $entry->fresh()->attendance);

        // Nothing left to pay.
        $this->post(route('labour.salary.store'), [
            'worker_id' => $worker->id,
            'period_from' => today()->subDay()->toDateString(),
            'period_to' => today()->toDateString(),
            'payment_method' => 'cash',
            'paid_on' => today()->toDateString(),
        ])->assertSessionHasErrors('deductions');

        // An admin cancelling the payment releases the work again.
        $this->actingAs($this->admin)->delete(route('labour.salary.destroy', $payment))->assertRedirect(route('labour.salary'));
        $this->assertEquals(2, WorkEntry::whereNull('salary_payment_id')->count());
    }

    public function test_demo_seeder_builds_a_working_quarry_and_can_run_twice(): void
    {
        $this->seed(QuarryDemoSeeder::class);
        $this->seed(QuarryDemoSeeder::class);

        $this->assertEquals(6, Product::count());
        $this->assertEquals(12, Worker::count());
        $this->assertTrue(Product::all()->every(fn ($product) => $product->total_cost > 0 && $product->profit() > 0));

        // Every entry's total matches its wage plus its work lines.
        WorkEntry::with('items')->get()->each(function ($entry) {
            $this->assertEqualsWithDelta((float) $entry->daily_wage + (float) $entry->items->sum('amount'), (float) $entry->total_earnings, 0.001);
        });

        // Last week is paid, this week is still owed.
        $this->assertGreaterThan(0, SalaryPayment::count());
        $this->assertTrue(WorkEntry::whereNull('salary_payment_id')->exists());
        SalaryPayment::with('workEntries')->get()->each(function ($payment) {
            $this->assertEqualsWithDelta((float) $payment->workEntries->sum('total_earnings') - (float) $payment->deductions, (float) $payment->net_amount, 0.001);
        });

        $this->actingAs($this->admin)->get(route('labour.production', ['from' => today()->subDays(13)->toDateString()]))->assertOk();
    }

    public function test_help_page_renders_in_english_and_sinhala(): void
    {
        $this->actingAs($this->cashier)->get(route('help'))
            ->assertOk()
            ->assertSee('Every evening: enter the day')
            ->assertDontSee('Admin: first-time setup');

        $this->actingAs($this->admin)->withSession(['locale' => 'si'])->get(route('help'))
            ->assertOk()
            ->assertSee('පද්ධතිය භාවිතා කරන ආකාරය')
            ->assertSee('පරිපාලක: පළමු වරට සැකසීම');
    }

    public function test_labour_pages_render(): void
    {
        $product = $this->makeProduct('Product A');
        $worker = Worker::create(['code' => 'WRK-0001', 'name' => 'Sunil', 'branch_id' => $this->branch->id, 'pay_type' => 'monthly', 'monthly_salary' => 60000]);
        $this->post(route('labour.work.store'), [
            'worker_id' => $worker->id, 'branch_id' => $this->branch->id,
            'work_date' => today()->toDateString(), 'attendance' => 'present',
            'items' => [['product_id' => $product->id, 'work_activity_id' => $this->activity('Loading')->id, 'quantity' => 3]],
        ]);
        $entry = WorkEntry::firstOrFail();

        foreach ([
            route('home'), route('labour'), route('labour.workers'), route('labour.workers.create'),
            route('labour.workers.edit', $worker), route('labour.work'), route('labour.work.create'),
            route('labour.work.edit', $entry), route('labour.salary'), route('labour.salary.create', ['worker' => $worker->id]),
            route('labour.production'), route('products'), route('products.create'), route('products.edit', $product),
            route('products.materials'), route('products.activities'),
        ] as $url) {
            $this->get($url)->assertOk();
        }

        // Monthly staff: payday pre-fills the monthly salary.
        $this->get(route('labour.salary.create', ['worker' => $worker->id]))->assertSee('60000');
    }
}
