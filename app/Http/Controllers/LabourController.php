<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Product;
use App\Models\SalaryPayment;
use App\Models\WorkActivity;
use App\Models\WorkEntry;
use App\Models\WorkEntryItem;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LabourController extends Controller
{
    /**
     * Workers menu: register labourers and look after their details.
     */
    public function index()
    {
        $stats = [
            'active' => Worker::where('is_active', true)->count(),
            'inactive' => Worker::where('is_active', false)->count(),
        ];

        return view('frontend.labour.main.index', compact('stats'));
    }

    /**
     * Attendance menu: check in / check out, and for admins the board, history and who checks in.
     */
    public function attendance()
    {
        $today = now()->toDateString();

        $stats = [
            'working_now' => Attendance::open()->whereDate('work_date', $today)->count(),
            'came_today' => Attendance::whereDate('work_date', $today)
                ->get(['attendable_type', 'attendable_id'])
                ->unique(fn (Attendance $shift) => $shift->attendable_type.':'.$shift->attendable_id)
                ->count(),
        ];

        return view('frontend.attendance.menu.index', compact('stats'));
    }

    /**
     * Salary & work menu: the day's work sheet, paying wages and the production report.
     */
    public function salary()
    {
        $today = now()->toDateString();
        $monthRange = [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];

        $stats = [
            'workers' => Worker::where('is_active', true)->count(),
            'present_today' => WorkEntry::whereDate('work_date', $today)->whereIn('attendance', ['present', 'half_day'])->count(),
            'earned_today' => (float) WorkEntry::whereDate('work_date', $today)->sum('total_earnings'),
            'unpaid' => (float) WorkEntry::whereNull('salary_payment_id')->sum('total_earnings'),
            'paid_month' => (float) SalaryPayment::whereBetween('paid_on', $monthRange)->sum('net_amount'),
        ];

        $todayTotals = $this->productTotals($today, $today);

        return view('frontend.labour.salary.menu.index', compact('stats', 'todayTotals'));
    }

    /**
     * Produced vs dispatched quantities per product over a date range, with the
     * quantity of every work activity and the labour paid for it.
     */
    public function production(Request $request)
    {
        $from = $request->date('from')?->toDateString() ?? now()->startOfMonth()->toDateString();
        $to = $request->date('to')?->toDateString() ?? now()->toDateString();
        $branchId = $request->query('branch_id');

        $branches = Branch::orderBy('name')->get();
        $activities = WorkActivity::orderByDesc('is_production')->orderBy('is_dispatch')->orderBy('name')->get();

        $rows = $this->itemsQuery($from, $to, $branchId)
            ->selectRaw('work_entry_items.product_id, work_entry_items.work_activity_id, SUM(work_entry_items.quantity) as quantity, SUM(work_entry_items.amount) as amount')
            ->groupBy('work_entry_items.product_id', 'work_entry_items.work_activity_id')
            ->get();

        $productIds = $rows->pluck('product_id')->unique();
        $products = Product::whereIn('id', $productIds)->orderBy('name')->get();

        $report = $products->map(function ($product) use ($rows, $activities) {
            $productRows = $rows->where('product_id', $product->id);
            $byActivity = $activities->mapWithKeys(fn ($activity) => [
                $activity->id => (float) $productRows->where('work_activity_id', $activity->id)->sum('quantity'),
            ]);

            $produced = $activities->where('is_production', true)->sum(fn ($activity) => $byActivity[$activity->id]);
            $dispatched = $activities->where('is_dispatch', true)->sum(fn ($activity) => $byActivity[$activity->id]);

            return [
                'product' => $product,
                'activities' => $byActivity,
                'produced' => $produced,
                'dispatched' => $dispatched,
                'balance' => $produced - $dispatched,
                'labour_paid' => (float) $productRows->sum('amount'),
            ];
        });

        $daily = $this->itemsQuery($from, $to, $branchId)
            ->join('work_activities', 'work_activities.id', '=', 'work_entry_items.work_activity_id')
            ->selectRaw('work_entries.work_date, work_entry_items.product_id')
            ->selectRaw('SUM(CASE WHEN work_activities.is_production = 1 THEN work_entry_items.quantity ELSE 0 END) as produced')
            ->selectRaw('SUM(CASE WHEN work_activities.is_dispatch = 1 THEN work_entry_items.quantity ELSE 0 END) as dispatched')
            ->groupBy('work_entries.work_date', 'work_entry_items.product_id')
            ->orderByDesc('work_entries.work_date')
            ->get()
            ->groupBy(fn ($row) => Carbon::parse($row->work_date)->toDateString());

        return view('frontend.labour.production.index', compact('from', 'to', 'branchId', 'branches', 'activities', 'report', 'daily', 'products'));
    }

    protected function itemsQuery(string $from, string $to, $branchId = null)
    {
        return WorkEntryItem::query()
            ->join('work_entries', 'work_entries.id', '=', 'work_entry_items.work_entry_id')
            ->whereDate('work_entries.work_date', '>=', $from)
            ->whereDate('work_entries.work_date', '<=', $to)
            ->when($branchId, fn ($query) => $query->where('work_entries.branch_id', $branchId));
    }

    protected function productTotals(string $from, string $to)
    {
        return $this->itemsQuery($from, $to)
            ->join('work_activities', 'work_activities.id', '=', 'work_entry_items.work_activity_id')
            ->join('products', 'products.id', '=', 'work_entry_items.product_id')
            ->selectRaw('products.name, products.unit')
            ->selectRaw('SUM(CASE WHEN work_activities.is_production = 1 THEN work_entry_items.quantity ELSE 0 END) as produced')
            ->selectRaw('SUM(CASE WHEN work_activities.is_dispatch = 1 THEN work_entry_items.quantity ELSE 0 END) as dispatched')
            ->groupBy('products.id', 'products.name', 'products.unit')
            ->orderBy('products.name')
            ->get();
    }
}
