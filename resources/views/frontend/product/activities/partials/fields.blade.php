{{-- Work activity fields. Optional $activity. --}}
@php
    $activity = $activity ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    $currentGroup = old('cost_group', $activity?->cost_group ?? 'labour');
@endphp

<div>
    <label for="name" class="{{ $label }}">{{ __('Work Type') }} <span class="text-accent">*</span></label>
    <input type="text" name="name" id="name" required maxlength="100" value="{{ old('name', $activity?->name) }}"
           placeholder="{{ __('e.g. Making, Sorting, Loading') }}" class="{{ $field }}">
    @error('name') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <p class="{{ $label }}">{{ __('Cost Group') }} <span class="text-accent">*</span></p>
    <div class="flex flex-wrap gap-2">
        @foreach (\App\Models\WorkActivity::COST_GROUPS as $value => $groupLabel)
            <label class="cursor-pointer">
                <input type="radio" name="cost_group" value="{{ $value }}" class="sr-only peer" {{ $currentGroup === $value ? 'checked' : '' }}>
                <span class="inline-block px-3.5 py-2 rounded-full text-sm font-medium border border-gray-300 dark:border-[#2a4a70] text-gray-600 dark:text-gray-300 bg-surface transition-colors
                             peer-checked:bg-[#004080] peer-checked:border-[#004080] peer-checked:text-white">
                    {{ __($groupLabel) }}
                </span>
            </label>
        @endforeach
    </div>
</div>

<div>
    <label for="default_rate" class="{{ $label }}">{{ __('Default Rate per Unit') }} <span class="text-accent">*</span></label>
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rs.</span>
        <input type="number" name="default_rate" id="default_rate" required step="0.01" min="0" inputmode="decimal"
               value="{{ old('default_rate', $activity ? (float) $activity->default_rate : '') }}" placeholder="0.00" class="{{ $field }} pl-10">
    </div>
    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ __('Pre-filled on new products. Each product can have its own rate.') }}</p>
    @error('default_rate') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
</div>

<div class="space-y-2">
    <p class="{{ $label }}">{{ __('Counts in production report as') }}</p>
    <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer">
        <input type="checkbox" name="is_production" value="1" class="rounded mt-0.5" {{ old('is_production', $activity?->is_production) ? 'checked' : '' }}>
        <span>{{ __('Produced') }} <span class="block text-[11px] text-gray-400">{{ __('Quantity adds to units made') }}</span></span>
    </label>
    <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer">
        <input type="checkbox" name="is_dispatch" value="1" class="rounded mt-0.5" {{ old('is_dispatch', $activity?->is_dispatch) ? 'checked' : '' }}>
        <span>{{ __('Dispatched') }} <span class="block text-[11px] text-gray-400">{{ __('Quantity adds to units loaded / sold out') }}</span></span>
    </label>
</div>

<div>
    <label for="description" class="{{ $label }}">{{ __('Description') }}</label>
    <input type="text" name="description" id="description" maxlength="255" value="{{ old('description', $activity?->description) }}"
           placeholder="{{ __('Optional') }}" class="{{ $field }}">
</div>

@if ($activity)
    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', $activity->is_active) ? 'checked' : '' }}>
        {{ __('Active') }}
    </label>
@endif
