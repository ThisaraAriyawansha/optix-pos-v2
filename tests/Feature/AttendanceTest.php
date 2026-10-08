<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\UserRole;
use App\Models\WorkActivity;
use App\Models\WorkEntry;
use App\Models\Worker;
use Database\Seeders\WorkActivitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected User $admin;

    protected User $cashier;

    protected Worker $worker;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-07 07:30:00');
        $this->seed(WorkActivitySeeder::class);

        $this->branch = Branch::create(['name' => 'Quarry 1', 'address' => 'Site', 'main_contact' => '0770000000']);
        $this->admin = User::factory()->create(['role_id' => UserRole::create(['name' => 'Super Admin'])->id, 'branch_id' => $this->branch->id]);
        $this->cashier = User::factory()->create(['role_id' => UserRole::create(['name' => 'Cashier'])->id, 'branch_id' => $this->branch->id]);
        $this->worker = Worker::create(['name' => 'Sunil', 'branch_id' => $this->branch->id, 'pay_type' => 'piece_rate']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_admins_are_never_on_the_check_in_sheet(): void
    {
        $this->actingAs($this->cashier)->get(route('attendance'))
            ->assertOk()
            ->assertSee('Sunil')
            ->assertSee($this->cashier->name)
            ->assertDontSee($this->admin->name);

        $this->post(route('attendance.checkIn'), ['type' => 'user', 'id' => $this->admin->id])->assertNotFound();
    }

    public function test_staff_check_in_and_out_manually(): void
    {
        $this->actingAs($this->cashier)
            ->post(route('attendance.checkIn'), ['type' => 'user', 'id' => $this->cashier->id])
            ->assertSessionHas('success');

        // Can't check in twice.
        $this->post(route('attendance.checkIn'), ['type' => 'user', 'id' => $this->cashier->id])->assertSessionHas('error');

        $shift = Attendance::sole();
        $this->assertSame('user', $shift->attendable_type);

        Carbon::setTestNow('2026-10-07 16:45:00');
        $this->post(route('attendance.checkOut', $shift))->assertSessionHas('success');

        $shift->refresh();
        $this->assertFalse($shift->isOpen());
        $this->assertSame(9 * 60 + 15, $shift->minutes());
        $this->assertSame(0, WorkEntry::count());
    }

    public function test_labourer_check_out_records_products_made_and_loaded(): void
    {
        $product = Product::create(['code' => 'P1', 'name' => '3/4 Metal', 'unit' => 'Cube']);
        $making = WorkActivity::where('name', 'Making')->firstOrFail();
        $loading = WorkActivity::where('name', 'Loading')->firstOrFail();
        $product->activityRates()->attach([$making->id => ['rate' => 3], $loading->id => ['rate' => 5]]);

        $this->actingAs($this->cashier)->post(route('attendance.checkIn'), ['type' => 'worker', 'id' => $this->worker->id]);
        $shift = Attendance::sole();

        Carbon::setTestNow('2026-10-07 17:00:00');
        $this->get(route('attendance.checkOut.form', $shift))->assertOk()->assertSee('3/4 Metal');

        $this->post(route('attendance.checkOut', $shift), [
            'attendance' => 'present',
            'items' => [
                ['product_id' => $product->id, 'work_activity_id' => $making->id, 'quantity' => 100],
                ['product_id' => $product->id, 'work_activity_id' => $loading->id, 'quantity' => 40],
            ],
        ])->assertRedirect(route('attendance'));

        $entry = WorkEntry::sole();
        $this->assertSame('present', $entry->attendance);
        $this->assertCount(2, $entry->items);
        $this->assertEquals(500, $entry->total_earnings);
        $this->assertFalse($shift->fresh()->isOpen());
    }

    public function test_only_admins_see_the_board_history_and_people_pages(): void
    {
        $this->actingAs($this->cashier)->get(route('attendance.board'))->assertForbidden();
        $this->get(route('attendance.report'))->assertForbidden();
        $this->get(route('attendance.people'))->assertForbidden();

        $this->post(route('attendance.checkIn'), ['type' => 'worker', 'id' => $this->worker->id]);
        Carbon::setTestNow('2026-10-07 10:00:00');

        $this->actingAs($this->admin)->get(route('attendance.board'))
            ->assertOk()
            ->assertSee('Sunil')
            ->assertSee('2h 30m');
        $this->get(route('attendance.report'))->assertOk()->assertSee('Sunil');
        $this->get(route('attendance.people'))->assertOk()
            ->assertSee('name="users[]" value="'.$this->cashier->id.'"', false)
            ->assertDontSee('name="users[]" value="'.$this->admin->id.'"', false);
    }

    public function test_admin_ticks_who_checks_in(): void
    {
        $this->actingAs($this->admin)->put(route('attendance.people.update'), [
            'workers' => [],
            'users' => [$this->cashier->id, $this->admin->id],
        ])->assertRedirect(route('attendance.people'));

        $this->assertFalse($this->worker->fresh()->track_attendance);
        $this->assertTrue($this->cashier->fresh()->track_attendance);

        $this->actingAs($this->cashier)->get(route('attendance'))->assertDontSee('Sunil');
    }

    public function test_fingerprint_punches_toggle_check_in_and_out(): void
    {
        config(['attendance.device_token' => 'secret']);
        $pin = (string) (int) substr($this->worker->code, 4);

        $this->postJson(route('attendance.device.punch'), ['pin' => $pin])->assertForbidden();

        $punch = fn (string $at) => $this->withHeader('X-Device-Token', 'secret')
            ->postJson(route('attendance.device.punch'), ['pin' => $pin, 'punched_at' => $at, 'device_sn' => 'GATE1']);

        $punch('2026-10-07 07:01:00')->assertOk()->assertJson(['result' => 'check_in', 'name' => 'Sunil']);
        $punch('2026-10-07 07:02:00')->assertOk()->assertJson(['result' => 'duplicate']);
        $punch('2026-10-07 07:01:00')->assertOk()->assertJson(['result' => 'check_in']); // re-sent log is not applied twice

        Carbon::setTestNow('2026-10-07 18:00:00');
        $punch('2026-10-07 16:31:00')->assertOk()->assertJson(['result' => 'check_out']);

        $shift = Attendance::sole();
        $this->assertSame('fingerprint', $shift->check_out_method);
        $this->assertSame(9 * 60 + 30, $shift->minutes());

        // The labourer's day lands on the work sheet so wages are counted.
        $this->assertSame('present', WorkEntry::sole()->attendance);

        $this->withHeader('X-Device-Token', 'secret')
            ->postJson(route('attendance.device.punch'), ['pin' => '9999'])
            ->assertNotFound()->assertJson(['result' => 'unknown']);
    }

    public function test_worker_can_work_at_a_different_branch_each_day(): void
    {
        $branchB = Branch::create(['name' => 'Quarry 2', 'address' => 'Hill', 'main_contact' => '0771111111']);

        // Monday: Sunil works at his home branch.
        $this->actingAs($this->cashier)->post(route('attendance.checkIn'), ['type' => 'worker', 'id' => $this->worker->id, 'branch_id' => $this->branch->id]);
        Carbon::setTestNow('2026-10-07 17:00:00');
        $this->post(route('attendance.checkOut', Attendance::sole()), ['attendance' => 'present']);

        // Tuesday: Quarry 2's sheet offers him as a visitor, and he checks in there.
        Carbon::setTestNow('2026-10-08 07:30:00');
        $this->get(route('attendance', ['branch_id' => $branchB->id]))
            ->assertOk()->assertSee('Working here today from another branch?')->assertSee('worker:'.$this->worker->id);

        $this->post(route('attendance.checkIn'), ['type' => 'worker', 'id' => $this->worker->id, 'branch_id' => $branchB->id])
            ->assertSessionHas('success');
        $tuesday = Attendance::latest('id')->first();
        $this->assertSame($branchB->id, (int) $tuesday->branch_id);

        // He is now on Quarry 2's sheet, marked as working there today.
        $this->get(route('attendance', ['branch_id' => $branchB->id]))->assertSee('Sunil')->assertSee('Today at Quarry 2');

        Carbon::setTestNow('2026-10-08 17:00:00');
        $this->post(route('attendance.checkOut', $tuesday), ['attendance' => 'present']);

        $days = WorkEntry::orderBy('work_date')->pluck('branch_id')->map(fn ($id) => (int) $id)->all();
        $this->assertSame([$this->branch->id, $branchB->id], $days);

        // His home branch's sheet shows where he was on Tuesday.
        $this->get(route('labour.work', ['date' => '2026-10-08', 'branch_id' => $this->branch->id]))
            ->assertOk()->assertSee('Worked at Quarry 2');
    }

    public function test_fingerprint_device_records_its_own_branch(): void
    {
        $branchB = Branch::create(['name' => 'Quarry 2', 'address' => 'Hill', 'main_contact' => '0771111111']);
        config(['attendance.device_token' => 'secret', 'attendance.device_branches' => ['GATE2' => $branchB->id]]);
        $pin = (string) (int) substr($this->worker->code, 4);

        $this->withHeader('X-Device-Token', 'secret')
            ->postJson(route('attendance.device.punch'), ['pin' => $pin, 'punched_at' => '2026-10-07 07:01:00', 'device_sn' => 'GATE2'])
            ->assertOk();

        $this->assertSame($branchB->id, (int) Attendance::sole()->branch_id);
    }

    public function test_zkteco_device_push(): void
    {
        $pin = (string) (int) substr($this->worker->code, 4);

        $this->get('/iclock/cdata?SN=ABC123&options=all')->assertForbidden();

        config(['attendance.device_serials' => ['ABC123']]);
        $this->get('/iclock/cdata?SN=ABC123&options=all')->assertOk()->assertSee('GET OPTION FROM: ABC123');

        $body = "{$pin}\t2026-10-07 07:05:00\t0\t1\t0\t0\n{$pin}\t2026-10-07 11:00:00\t1\t1\t0\t0\n";
        $this->call('POST', '/iclock/cdata?SN=ABC123&table=ATTLOG', [], [], [], ['CONTENT_TYPE' => 'text/plain'], $body)
            ->assertOk()->assertSee('OK: 2');

        $shift = Attendance::sole();
        $this->assertSame('11:00', $shift->check_out_at->format('H:i'));
        $this->assertSame('half_day', WorkEntry::sole()->attendance);
        $this->assertSame(2, AttendancePunch::count());
    }
}
