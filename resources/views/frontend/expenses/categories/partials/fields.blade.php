{{-- Shared expense type fields. Optional $category. --}}
@php
    $category = $category ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    $currentColor = old('color', $category?->color ?? 'blue');
    $currentIcon = old('icon', $category?->icon ?? 'receipt');
@endphp

<div>
    <label for="name" class="{{ $label }}">{{ __('Type Name') }} <span class="text-accent">*</span></label>
    <input type="text" name="name" id="name" maxlength="100" required
           value="{{ old('name', $category?->name) }}" placeholder="{{ __('e.g. Electricity') }}"
           class="{{ $field }}">
    @error('name')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="{{ $label }}">{{ __('Description') }}</label>
    <input type="text" name="description" id="description" maxlength="255"
           value="{{ old('description', $category?->description) }}" placeholder="{{ __('Optional') }}"
           class="{{ $field }}">
    @error('description')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <p class="{{ $label }}">{{ __('Color') }}</p>
    <div class="flex flex-wrap gap-2.5">
        @foreach (\App\Models\ExpenseCategory::COLORS as $key => $palette)
            <label class="cursor-pointer" title="{{ ucfirst($key) }}">
                <input type="radio" name="color" value="{{ $key }}" class="sr-only peer" {{ $currentColor === $key ? 'checked' : '' }}>
                <span class="block w-8 h-8 rounded-full {{ $palette['solid'] }} ring-offset-2 ring-offset-white dark:ring-offset-[#112236] peer-checked:ring-2 peer-checked:ring-gray-900 dark:peer-checked:ring-white transition"></span>
            </label>
        @endforeach
    </div>
    @error('color')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <p class="{{ $label }}">{{ __('Icon') }}</p>
    <div class="grid grid-cols-8 gap-2">
        @foreach (\App\Models\ExpenseCategory::ICONS as $key => $path)
            <label class="cursor-pointer" title="{{ ucfirst($key) }}">
                <input type="radio" name="icon" value="{{ $key }}" class="sr-only peer" {{ $currentIcon === $key ? 'checked' : '' }}>
                <span class="flex aspect-square items-center justify-center rounded-xl border border-gray-200 dark:border-[#2a4a70] text-gray-600 dark:text-gray-300 transition-colors
                             peer-checked:bg-[#004080] peer-checked:border-[#004080] peer-checked:text-white hover:border-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                    </svg>
                </span>
            </label>
        @endforeach
    </div>
    @error('icon')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>
