<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\WorkActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $canManage = $request->user()->can('manage-pricing');

        $products = Product::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            // Non-admins only see the active price list.
            ->when(! $canManage, fn ($query) => $query->where('is_active', true))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        return view('frontend.product.main.index', compact('products', 'search', 'canManage'));
    }

    public function create()
    {
        return view('frontend.product.addproduct.index', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = DB::transaction(function () use ($validated) {
            $product = Product::create($this->productAttributes($validated));
            $this->syncCostLines($product, $validated);

            return $product;
        });

        return redirect()->route('products')->with('success', __('Product :name saved successfully.', ['name' => $product->name]));
    }

    public function edit(Product $product)
    {
        $product->load(['materials', 'activityRates']);

        return view('frontend.product.updateproduct.index', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($product, $validated) {
            // Blank code on edit keeps the current one.
            $product->update(['code' => $validated['code'] ?? $product->code] + $this->productAttributes($validated));
            $this->syncCostLines($product, $validated);
        });

        return redirect()->route('products')->with('success', __('Product :name updated successfully.', ['name' => $product->name]));
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return redirect()->route('products')->with('success', __($product->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $product->name]));
    }

    protected function formData(?Product $product = null): array
    {
        $materials = RawMaterial::where('is_active', true)
            ->when($product, fn ($query) => $query->orWhereIn('id', $product->materials->pluck('id')))
            ->orderBy('name')
            ->get();

        $activities = WorkActivity::where('is_active', true)
            ->when($product, fn ($query) => $query->orWhereIn('id', $product->activityRates->pluck('id')))
            ->orderBy('cost_group', 'desc')
            ->orderBy('name')
            ->get();

        $nextCode = $product ? null : Product::nextCode();

        return compact('product', 'materials', 'activities', 'nextCode');
    }

    protected function validateProduct(Request $request, ?Product $product = null): array
    {
        $request->merge(['code' => Product::normalizeCode($request->input('code'))]);

        return $request->validate([
            'code' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-\/]+$/', Rule::unique('products', 'code')->ignore($product)],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product)],
            'unit' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'materials' => ['nullable', 'array'],
            'materials.*.raw_material_id' => ['required', 'distinct', 'exists:raw_materials,id'],
            'materials.*.quantity' => ['required', 'numeric', 'min:0.001', 'max:999999'],
            'rates' => ['nullable', 'array'],
            'rates.*' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'commission_type' => ['required', Rule::in(['fixed', 'percent'])],
            'commission_value' => ['required', 'numeric', 'min:0', 'max:9999999999', Rule::when($request->input('commission_type') === 'percent', ['max:100'])],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'materials.*.raw_material_id' => __('raw material'),
            'materials.*.quantity' => __('material quantity'),
            'rates.*' => __('rate'),
        ]);
    }

    protected function productAttributes(array $validated): array
    {
        return [
            'code' => $validated['code'] ?? null,
            'name' => $validated['name'],
            'unit' => $validated['unit'],
            'description' => $validated['description'] ?? null,
            'commission_type' => $validated['commission_type'],
            'commission_value' => $validated['commission_value'],
            'selling_price' => $validated['selling_price'],
            'is_active' => $validated['is_active'] ?? true,
        ];
    }

    protected function syncCostLines(Product $product, array $validated): void
    {
        $product->materials()->sync(
            collect($validated['materials'] ?? [])
                ->mapWithKeys(fn ($line) => [$line['raw_material_id'] => ['quantity' => $line['quantity']]])
                ->all()
        );

        // A blank rate means the activity does not apply to this product.
        $activityIds = WorkActivity::pluck('id')->all();
        $product->activityRates()->sync(
            collect($validated['rates'] ?? [])
                ->filter(fn ($rate, $activityId) => filled($rate) && in_array((int) $activityId, $activityIds, true))
                ->map(fn ($rate) => ['rate' => $rate])
                ->all()
        );

        $product->recalculateCosts();
    }
}
