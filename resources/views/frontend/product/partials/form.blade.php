{{-- Product form with live cost build-up. Expects $materials, $activities; optional $product, $nextCode. --}}
@php
    $product = $product ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    $card = 'rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-4';

    $materialLines = old('materials', $product?->materials->map(fn ($material) => [
        'raw_material_id' => $material->id,
        'quantity' => (float) $material->pivot->quantity,
    ])->values()->all() ?? []);

    $rates = old('rates', $activities->mapWithKeys(function ($activity) use ($product) {
        if (! $product) {
            return [$activity->id => (float) $activity->default_rate];
        }
        $attached = $product->activityRates->firstWhere('id', $activity->id);

        return [$activity->id => $attached ? (float) $attached->pivot->rate : ''];
    })->all());

    $commissionType = old('commission_type', $product?->commission_type ?? 'fixed');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
    <div class="lg:col-span-2 space-y-4">

        {{-- ── details ── --}}
        <div class="{{ $card }}">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Product Details') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label for="code" class="{{ $label }}">{{ __('Product Code') }}</label>
                    <input type="text" name="code" id="code" maxlength="20" value="{{ old('code', $product?->code ?? $nextCode ?? '') }}"
                           class="{{ $field }} uppercase">
                    @error('code') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="name" class="{{ $label }}">{{ __('Product Name') }} <span class="text-accent">*</span></label>
                    <input type="text" name="name" id="name" required maxlength="255" value="{{ old('name', $product?->name) }}"
                           placeholder="{{ __('e.g. 3/4 Metal, Quarry Dust, ABC') }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="unit" class="{{ $label }}">{{ __('Sold per') }} <span class="text-accent">*</span></label>
                    <input type="text" name="unit" id="unit" required maxlength="30" list="unit-options"
                           value="{{ old('unit', $product?->unit ?? 'Cube') }}" class="{{ $field }}">
                    <datalist id="unit-options">
                        @foreach (\App\Models\Product::UNITS as $unit)
                            <option value="{{ $unit }}">
                        @endforeach
                    </datalist>
                </div>
            </div>
            <div>
                <label for="description" class="{{ $label }}">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="2" maxlength="1000" class="{{ $field }} resize-none"
                          placeholder="{{ __('Optional') }}">{{ old('description', $product?->description) }}</textarea>
            </div>
        </div>

        {{-- ── raw materials ── --}}
        <div class="{{ $card }}">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('1. Raw Materials') }}</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Materials used to make one :unit', ['unit' => __('unit')]) }}</p>
                </div>
                <a href="{{ route('products.materials') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand">{{ __('Manage materials') }} →</a>
            </div>

            @if ($materials->isEmpty())
                <div class="px-4 py-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No raw materials yet.') }}
                    <a href="{{ route('products.materials') }}" class="text-brand font-medium underline">{{ __('Add materials first') }}</a>
                </div>
            @else
                <div id="material-lines" class="space-y-2"></div>
                <button type="button" onclick="addMaterialLine()"
                        class="w-full py-2.5 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-brand hover:border-gray-400 transition-colors">
                    + {{ __('Add material') }}
                </button>
            @endif
        </div>

        {{-- ── labour & handling rates ── --}}
        <div class="{{ $card }}">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('2. Labour & Handling') }}</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Paid to workers per unit. Leave blank if the work does not apply.') }}</p>
                </div>
                <a href="{{ route('products.activities') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand">{{ __('Manage work types') }} →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($activities as $activity)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-alt border border-subtle">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $activity->label() }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $activity->costGroupLabel() }}</p>
                        </div>
                        <div class="relative w-32 shrink-0">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rs.</span>
                            <input type="number" name="rates[{{ $activity->id }}]" step="0.01" min="0" inputmode="decimal"
                                   value="{{ $rates[$activity->id] ?? '' }}" placeholder="—"
                                   data-group="{{ $activity->cost_group }}"
                                   class="rate-input {{ $field }} pl-10 py-2 text-right" oninput="recalc()">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── commission & price ── --}}
        <div class="{{ $card }}">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('3. Commission & Selling Price') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="{{ $label }}">{{ __('Commission') }} <span class="text-accent">*</span></p>
                    <div class="flex gap-2">
                        <select name="commission_type" id="commission_type" class="{{ $field }} w-28 shrink-0" onchange="recalc()">
                            <option value="fixed" {{ $commissionType === 'fixed' ? 'selected' : '' }}>Rs.</option>
                            <option value="percent" {{ $commissionType === 'percent' ? 'selected' : '' }}>% {{ __('of cost') }}</option>
                        </select>
                        <input type="number" name="commission_value" id="commission_value" step="0.01" min="0" required inputmode="decimal"
                               value="{{ old('commission_value', $product ? (float) $product->commission_value : 0) }}" class="{{ $field }}" oninput="recalc()">
                    </div>
                </div>
                <div>
                    <label for="selling_price" class="{{ $label }}">{{ __('Selling Price') }} <span class="text-accent">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rs.</span>
                        <input type="number" name="selling_price" id="selling_price" step="0.01" min="0" required inputmode="decimal"
                               value="{{ old('selling_price', $product ? (float) $product->selling_price : '') }}" placeholder="0.00"
                               class="{{ $field }} pl-12 text-lg font-semibold" oninput="recalc()">
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ __('Per unit, before any discount') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── live cost summary ── --}}
    <aside class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-3">
        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Cost per Unit') }}</h2>
        <dl class="space-y-2 text-sm font-sans">
            @foreach (['material' => __('Raw materials'), 'labour' => __('Labour'), 'handling' => __('Handling')] as $key => $text)
                <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ $text }}</dt><dd id="sum-{{ $key }}">Rs. 0.00</dd></div>
            @endforeach
            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-[#24446a] font-semibold text-gray-900 dark:text-white"><dt>{{ __('Total Cost') }}</dt><dd id="sum-total">Rs. 0.00</dd></div>
            <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>+ {{ __('Commission') }}</dt><dd id="sum-commission">Rs. 0.00</dd></div>
            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-[#24446a] text-gray-900 dark:text-white"><dt>{{ __('Break-even Price') }}</dt><dd id="sum-breakeven" class="font-semibold">Rs. 0.00</dd></div>
            <div class="flex justify-between text-gray-900 dark:text-white"><dt>{{ __('Selling Price') }}</dt><dd id="sum-price" class="font-semibold">Rs. 0.00</dd></div>
        </dl>
        <div id="profit-box" class="rounded-xl px-4 py-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400">
            <p class="text-xs">{{ __('Profit per unit') }}</p>
            <p class="font-heading font-semibold text-xl" id="sum-profit">Rs. 0.00</p>
            <p class="text-[11px]" id="sum-margin"></p>
        </div>

        @if ($product)
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer pt-1">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                {{ __('Active (available for sale & work entry)') }}
            </label>
        @endif

        <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
            {{ $product ? __('Update Product') : __('Save Product') }}
        </button>
    </aside>
</div>

<template id="material-line-template">
    <div class="material-line flex items-center gap-2">
        <select data-name="raw_material_id" class="{{ $field }} flex-1 min-w-0" onchange="recalc()" required>
            <option value="" disabled selected>{{ __('Select material') }}</option>
            @foreach ($materials as $material)
                <option value="{{ $material->id }}">{{ $material->name }} ({{ \App\Support\Money::format($material->unit_cost) }} / {{ $material->unit }})</option>
            @endforeach
        </select>
        <input type="number" data-name="quantity" step="0.001" min="0.001" placeholder="{{ __('Qty') }}" required inputmode="decimal"
               class="{{ $field }} w-24 sm:w-28 shrink-0 text-right" oninput="recalc()">
        <span data-role="unit" class="w-12 text-xs text-gray-400 shrink-0 truncate"></span>
        <span data-role="cost" class="hidden sm:block w-24 text-right text-sm text-gray-700 dark:text-gray-200 shrink-0">Rs. 0.00</span>
        <button type="button" onclick="this.closest('.material-line').remove(); renumber(); recalc();" title="{{ __('Remove') }}"
                class="w-9 h-9 shrink-0 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</template>

@php
    $materialInfo = $materials->mapWithKeys(fn ($material) => [$material->id => ['cost' => (float) $material->unit_cost, 'unit' => $material->unit]]);
@endphp
<script>
    const MATERIALS = @json($materialInfo);
    const money = (value) => 'Rs. ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const num = (value) => parseFloat(value) || 0;

    function addMaterialLine(line = {}) {
        const container = document.getElementById('material-lines');
        if (!container) return;
        const node = document.getElementById('material-line-template').content.firstElementChild.cloneNode(true);
        if (line.raw_material_id) node.querySelector('[data-name=raw_material_id]').value = line.raw_material_id;
        if (line.quantity) node.querySelector('[data-name=quantity]').value = line.quantity;
        container.appendChild(node);
        renumber();
        recalc();
    }

    function renumber() {
        document.querySelectorAll('#material-lines .material-line').forEach((row, index) => {
            row.querySelectorAll('[data-name]').forEach((input) => input.name = `materials[${index}][${input.dataset.name}]`);
        });
    }

    function recalc() {
        let material = 0;
        document.querySelectorAll('#material-lines .material-line').forEach((row) => {
            const info = MATERIALS[row.querySelector('[data-name=raw_material_id]').value];
            const cost = info ? info.cost * num(row.querySelector('[data-name=quantity]').value) : 0;
            row.querySelector('[data-role=unit]').textContent = info ? info.unit : '';
            row.querySelector('[data-role=cost]').textContent = money(cost);
            material += cost;
        });

        const groups = { labour: 0, handling: 0 };
        document.querySelectorAll('.rate-input').forEach((input) => groups[input.dataset.group] += num(input.value));

        const total = material + groups.labour + groups.handling;
        const commissionValue = num(document.getElementById('commission_value').value);
        const commission = document.getElementById('commission_type').value === 'percent' ? total * commissionValue / 100 : commissionValue;
        const breakEven = total + commission;
        const price = num(document.getElementById('selling_price').value);
        const profit = price - breakEven;

        document.getElementById('sum-material').textContent = money(material);
        document.getElementById('sum-labour').textContent = money(groups.labour);
        document.getElementById('sum-handling').textContent = money(groups.handling);
        document.getElementById('sum-total').textContent = money(total);
        document.getElementById('sum-commission').textContent = money(commission);
        document.getElementById('sum-breakeven').textContent = money(breakEven);
        document.getElementById('sum-price').textContent = money(price);
        document.getElementById('sum-profit').textContent = money(profit);
        document.getElementById('sum-margin').textContent = price > 0 ? (profit / price * 100).toFixed(1) + '% {{ __('margin') }}' : '';
        document.getElementById('profit-box').className = 'rounded-xl px-4 py-3 ' + (profit < 0
            ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'
            : 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400');
    }

    @json(array_values($materialLines)).forEach((line) => addMaterialLine(line));
    recalc();
</script>
