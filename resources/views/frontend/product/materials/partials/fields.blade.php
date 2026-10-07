{{-- Raw material fields. Optional $material. --}}
@php
    $material = $material ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
@endphp

<div>
    <label for="name" class="{{ $label }}">{{ __('Material Name') }} <span class="text-accent">*</span></label>
    <input type="text" name="name" id="name" required maxlength="255" value="{{ old('name', $material?->name) }}"
           placeholder="{{ __('e.g. Explosives, Diesel, Detonators') }}" class="{{ $field }}">
    @error('name') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label for="unit_cost" class="{{ $label }}">{{ __('Cost per Unit') }} <span class="text-accent">*</span></label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rs.</span>
            <input type="number" name="unit_cost" id="unit_cost" required step="0.01" min="0" inputmode="decimal"
                   value="{{ old('unit_cost', $material ? (float) $material->unit_cost : '') }}" placeholder="0.00" class="{{ $field }} pl-10">
        </div>
        @error('unit_cost') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="unit" class="{{ $label }}">{{ __('Unit') }} <span class="text-accent">*</span></label>
        <input type="text" name="unit" id="unit" required maxlength="30" list="material-units"
               value="{{ old('unit', $material?->unit) }}" placeholder="{{ __('kg, litre, piece') }}" class="{{ $field }}">
        <datalist id="material-units">
            @foreach (['kg', 'litre', 'piece', 'ton', 'cube', 'bag'] as $unit)
                <option value="{{ $unit }}">
            @endforeach
        </datalist>
        @error('unit') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="description" class="{{ $label }}">{{ __('Description') }}</label>
    <input type="text" name="description" id="description" maxlength="255" value="{{ old('description', $material?->description) }}"
           placeholder="{{ __('Optional') }}" class="{{ $field }}">
</div>

@if ($material)
    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', $material->is_active) ? 'checked' : '' }}>
        {{ __('Active') }}
    </label>
@endif
