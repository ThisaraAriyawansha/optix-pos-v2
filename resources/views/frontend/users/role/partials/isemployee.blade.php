{{-- "Is this role an employee?" Yes / No picker. Expects $value (bool). --}}
<fieldset>
    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
        {{ __('Are people in this role employees?') }} <span class="text-accent">*</span>
    </legend>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach ([
            1 => [
                'title' => __('Yes, Employee'),
                'hint' => __('Gets an Employee ID, checks in / out and can be paid'),
                'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
                'example' => __('e.g. Cashier, Manager, Driver'),
            ],
            0 => [
                'title' => __('No, Owner'),
                'hint' => __('No Employee ID, no check in / out'),
                'icon' => 'M5 3l3.057 3.057L12 3l3.943 3.057L19 3v13a2 2 0 01-2 2H7a2 2 0 01-2-2V3zm0 18h14',
                'example' => __('e.g. Owner, Partner, Super Admin'),
            ],
        ] as $choice => $option)
            <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-colors
                          border-subtle has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-blue-50 dark:has-[:checked]:border-white dark:has-[:checked]:bg-white/5">
                <input type="radio" name="is_employee" value="{{ $choice }}" required class="peer sr-only"
                       {{ (string) (int) $value === (string) $choice ? 'checked' : '' }}>
                <span class="w-11 h-11 shrink-0 rounded-xl bg-surface-alt flex items-center justify-center text-gray-700 dark:text-gray-300 peer-checked:bg-[var(--color-primary)] peer-checked:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $option['icon'] }}"/>
                    </svg>
                </span>
                <span class="flex flex-col gap-0.5 min-w-0">
                    <span class="font-semibold text-[15px] text-gray-900 dark:text-white">{{ $option['title'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $option['hint'] }}</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $option['example'] }}</span>
                </span>
                <span class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 dark:border-[#2a4a70] peer-checked:border-[var(--color-primary)] peer-checked:bg-[var(--color-primary)] flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
            </label>
        @endforeach
    </div>

    @error('is_employee')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</fieldset>
