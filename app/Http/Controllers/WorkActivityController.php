<?php

namespace App\Http\Controllers;

use App\Models\WorkActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkActivityController extends Controller
{
    public function index()
    {
        $activities = WorkActivity::withCount('products')
            ->orderByDesc('is_active')
            ->orderBy('cost_group', 'desc')
            ->orderBy('name')
            ->get();

        return view('frontend.product.activities.main.index', compact('activities'));
    }

    public function store(Request $request)
    {
        WorkActivity::create($this->validateActivity($request));

        return redirect()->route('products.activities')->with('success', __('Work activity added successfully.'));
    }

    public function edit(WorkActivity $activity)
    {
        return view('frontend.product.activities.update.index', compact('activity'));
    }

    public function update(Request $request, WorkActivity $activity)
    {
        $validated = $this->validateActivity($request, $activity);
        $validated['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($activity, $validated) {
            $activity->update($validated);

            // Moving an activity between labour and handling changes each product's cost split.
            $activity->products->each->recalculateCosts();
        });

        return redirect()->route('products.activities')->with('success', __('Work activity updated successfully.'));
    }

    public function toggleStatus(WorkActivity $activity)
    {
        $activity->update(['is_active' => ! $activity->is_active]);

        return redirect()->route('products.activities')->with('success', __($activity->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $activity->name]));
    }

    protected function validateActivity(Request $request, ?WorkActivity $activity = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('work_activities', 'name')->ignore($activity)],
            'cost_group' => ['required', Rule::in(array_keys(WorkActivity::COST_GROUPS))],
            'default_rate' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_production'] = $request->boolean('is_production');
        $validated['is_dispatch'] = $request->boolean('is_dispatch');

        return $validated;
    }
}
