<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::withCount('products')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('frontend.product.materials.main.index', compact('materials'));
    }

    public function store(Request $request)
    {
        RawMaterial::create($this->validateMaterial($request));

        return redirect()->route('products.materials')->with('success', __('Raw material added successfully.'));
    }

    public function edit(RawMaterial $material)
    {
        $material->load('products');

        return view('frontend.product.materials.update.index', compact('material'));
    }

    public function update(Request $request, RawMaterial $material)
    {
        $validated = $this->validateMaterial($request, $material);
        $validated['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($material, $validated) {
            $material->update($validated);

            // A new unit cost changes the cost of every product made from this material.
            $material->products->each->recalculateCosts();
        });

        return redirect()->route('products.materials')->with('success', __('Raw material updated. :count product costs recalculated.', ['count' => $material->products->count()]));
    }

    public function toggleStatus(RawMaterial $material)
    {
        $material->update(['is_active' => ! $material->is_active]);

        return redirect()->route('products.materials')->with('success', __($material->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $material->name]));
    }

    protected function validateMaterial(Request $request, ?RawMaterial $material = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('raw_materials', 'name')->ignore($material)],
            'unit' => ['required', 'string', 'max:30'],
            'unit_cost' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
