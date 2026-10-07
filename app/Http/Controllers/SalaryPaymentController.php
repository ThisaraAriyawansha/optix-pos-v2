<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\SalaryPayment;
use App\Models\WorkEntry;
use App\Models\Worker;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalaryPaymentController extends Controller
{
    public function index(Request $request)
    {
        $workers = Worker::query()
            ->with('branch')
            ->withSum(['workEntries as unpaid_total' => fn ($query) => $query->whereNull('salary_payment_id')], 'total_earnings')
            ->withCount(['workEntries as unpaid_days' => fn ($query) => $query->whereNull('salary_payment_id')])
            ->withMax('salaryPayments as last_paid_on', 'paid_on')
            ->where('is_active', true)
            ->orderByDesc('unpaid_total')
            ->orderBy('name')
            ->get();

        $payments = SalaryPayment::with(['worker', 'user'])
            ->latest('paid_on')
            ->latest('id')
            ->paginate(15);

        $stats = [
            'unpaid' => (float) $workers->sum('unpaid_total'),
            'paid_month' => (float) SalaryPayment::whereBetween('paid_on', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->sum('net_amount'),
        ];

        return view('frontend.labour.salary.main.index', compact('workers', 'payments', 'stats'));
    }

    public function create(Request $request)
    {
        $workers = Worker::where('is_active', true)->orderBy('name')->get();
        $worker = $request->query('worker') ? Worker::find($request->query('worker')) : null;

        $from = $request->query('from');
        $to = $request->query('to', now()->toDateString());
        $entries = collect();

        if ($worker) {
            // Default the period to start at the oldest unpaid day so nothing is missed.
            $from ??= WorkEntry::where('worker_id', $worker->id)->whereNull('salary_payment_id')->min('work_date')
                ?? now()->startOfMonth()->toDateString();
            $entries = $this->unpaidEntries($worker, $from, $to)->with(['items.product', 'items.activity'])->get();
        }

        $from ??= now()->startOfMonth()->toDateString();

        return view('frontend.labour.salary.add.index', compact('workers', 'worker', 'from', 'to', 'entries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => ['required', 'exists:workers,id'],
            'period_from' => ['required', 'date'],
            'period_to' => ['required', 'date', 'after_or_equal:period_from'],
            'basic_salary' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'bonus' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'deductions' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $worker = Worker::findOrFail($validated['worker_id']);

        $payment = DB::transaction(function () use ($validated, $worker, $request) {
            $entries = $this->unpaidEntries($worker, $validated['period_from'], $validated['period_to'])->lockForUpdate()->get();

            $workEarnings = (float) $entries->sum('total_earnings');
            $basic = (float) ($validated['basic_salary'] ?? 0);
            $bonus = (float) ($validated['bonus'] ?? 0);
            $deductions = (float) ($validated['deductions'] ?? 0);
            $net = round($workEarnings + $basic + $bonus - $deductions, 2);

            if ($net <= 0) {
                throw ValidationException::withMessages([
                    'deductions' => __('Nothing to pay: the net amount must be more than zero.'),
                ]);
            }

            $payment = SalaryPayment::create([
                'payment_code' => $this->generatePaymentCode(),
                'worker_id' => $worker->id,
                'branch_id' => $worker->branch_id,
                'user_id' => $request->user()->id,
                'period_from' => $validated['period_from'],
                'period_to' => $validated['period_to'],
                'work_earnings' => $workEarnings,
                'basic_salary' => $basic,
                'bonus' => $bonus,
                'deductions' => $deductions,
                'net_amount' => $net,
                'payment_method' => $validated['payment_method'],
                'paid_on' => $validated['paid_on'],
                'notes' => $validated['notes'] ?? null,
            ]);

            WorkEntry::whereIn('id', $entries->pluck('id'))->update(['salary_payment_id' => $payment->id]);

            return $payment;
        });

        return redirect()->route('labour.salary.show', $payment)
            ->with('success', __('Salary :code of :amount paid to :name.', ['code' => $payment->payment_code, 'amount' => Money::format($payment->net_amount), 'name' => $worker->name]));
    }

    public function show(SalaryPayment $payment)
    {
        $payment->load(['worker.branch', 'user', 'workEntries' => fn ($query) => $query->orderBy('work_date'), 'workEntries.items.product', 'workEntries.items.activity']);

        return view('frontend.labour.salary.show.index', compact('payment'));
    }

    public function destroy(SalaryPayment $payment)
    {
        // Deleting the payment releases its work entries back to unpaid (FK is nullOnDelete).
        $payment->delete();

        return redirect()->route('labour.salary')->with('success', __('Salary :code cancelled. Its work is unpaid again.', ['code' => $payment->payment_code]));
    }

    protected function unpaidEntries(Worker $worker, string $from, string $to)
    {
        return WorkEntry::query()
            ->where('worker_id', $worker->id)
            ->whereNull('salary_payment_id')
            ->whereDate('work_date', '>=', $from)
            ->whereDate('work_date', '<=', $to)
            ->orderBy('work_date');
    }

    protected function generatePaymentCode(): string
    {
        $lastId = SalaryPayment::max('id') ?? 0;

        return 'SAL-'.str_pad((string) ($lastId + 1), 5, '0', STR_PAD_LEFT);
    }
}
