{{-- Worker form fields. Expects $branches; optional $worker, $nextCode. --}}
@php
    $worker = $worker ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    $currentPayType = old('pay_type', $worker?->pay_type ?? 'piece_rate');
    $currentBranch = old('branch_id', $worker?->branch_id ?? auth()->user()?->branch_id);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="code" class="{{ $label }}">{{ __('Employee ID') }}</label>
        <input type="text" name="code" id="code" maxlength="20" value="{{ old('code', $worker?->code ?? $nextCode ?? '') }}" class="{{ $field }} uppercase">
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Shared with system users. Leave as is to use the next free ID.') }}</p>
        @error('code') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="name" class="{{ $label }}">{{ __('Full Name') }} <span class="text-accent">*</span></label>
        <input type="text" name="name" id="name" required maxlength="255" value="{{ old('name', $worker?->name) }}" class="{{ $field }}">
        @error('name') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="branch_id" class="{{ $label }}">{{ __('Branch') }} <span class="text-accent">*</span></label>
        <select name="branch_id" id="branch_id" required class="{{ $field }}">
            <option value="" disabled {{ $currentBranch ? '' : 'selected' }}>{{ __('Select a branch') }}</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" {{ (string) $currentBranch === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="nic" class="{{ $label }}">{{ __('NIC No.') }}</label>
        <input type="text" name="nic" id="nic" maxlength="20" value="{{ old('nic', $worker?->nic) }}" class="{{ $field }}">
        @error('nic') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="phone" class="{{ $label }}">{{ __('Phone') }}</label>
        <input type="tel" name="phone" id="phone" maxlength="20" value="{{ old('phone', $worker?->phone) }}" class="{{ $field }}">
    </div>
</div>

{{-- ── pay type ── --}}
<div>
    <p class="{{ $label }}">{{ __('How is this worker paid?') }} <span class="text-accent">*</span></p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
        @foreach ([
            'piece_rate' => __('By the work done each day, e.g. Rs. 3 per unit made, Rs. 5 per unit loaded'),
            'daily' => __('A fixed amount for each day present (half for a half day)'),
            'monthly' => __('A fixed monthly salary — for office & top-level staff'),
        ] as $value => $hint)
            <label class="cursor-pointer">
                <input type="radio" name="pay_type" value="{{ $value }}" class="sr-only peer" onchange="togglePayFields()"
                       {{ $currentPayType === $value ? 'checked' : '' }}>
                <div class="h-full p-3 rounded-xl border-2 border-gray-200 dark:border-[#2a4a70] bg-surface transition-all
                            peer-checked:border-[#004080] dark:peer-checked:border-blue-400 peer-checked:bg-blue-50/60 dark:peer-checked:bg-blue-500/10">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __(\App\Models\Worker::PAY_TYPES[$value]) }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $hint }}</p>
                </div>
            </label>
        @endforeach
    </div>
    @error('pay_type') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div data-pay="daily">
        <label for="daily_rate" class="{{ $label }}">{{ __('Daily Wage') }} <span class="text-accent">*</span></label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rs.</span>
            <input type="number" name="daily_rate" id="daily_rate" step="0.01" min="0" inputmode="decimal"
                   value="{{ old('daily_rate', $worker?->daily_rate ? (float) $worker->daily_rate : '') }}" class="{{ $field }} pl-10">
        </div>
        @error('daily_rate') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div data-pay="monthly">
        <label for="monthly_salary" class="{{ $label }}">{{ __('Monthly Salary') }} <span class="text-accent">*</span></label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Rs.</span>
            <input type="number" name="monthly_salary" id="monthly_salary" step="0.01" min="0" inputmode="decimal"
                   value="{{ old('monthly_salary', $worker?->monthly_salary ? (float) $worker->monthly_salary : '') }}" class="{{ $field }} pl-10">
        </div>
        @error('monthly_salary') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
    </div>
    <div data-pay="piece_rate" class="sm:col-span-2 px-4 py-3 rounded-xl bg-surface-alt text-xs text-gray-500 dark:text-gray-400 font-sans">
        {{ __('Piece rates are set per product under Products → Work Rates.') }}
    </div>
    <div>
        <label for="joined_on" class="{{ $label }}">{{ __('Joined On') }}</label>
        <input type="date" name="joined_on" id="joined_on" max="{{ now()->toDateString() }}"
               value="{{ old('joined_on', $worker?->joined_on?->toDateString()) }}" class="{{ $field }}">
    </div>
</div>

{{-- ── EPF / ETF ── --}}
<div class="rounded-xl border border-gray-300 dark:border-[#2a4a70] p-4 space-y-4">
    <label class="flex items-start gap-2 cursor-pointer">
        <input type="hidden" name="epf_enabled" value="0">
        <input type="checkbox" name="epf_enabled" id="epf_enabled" value="1" class="rounded mt-0.5" onchange="toggleEpfFields()"
               {{ old('epf_enabled', $worker?->epf_enabled) ? 'checked' : '' }}>
        <span>
            <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ __('Calculate EPF / ETF') }}</span>
            <span class="block text-[11px] text-gray-400 dark:text-gray-500">{{ __('Leave unticked for workers not registered for EPF / ETF.') }}</span>
        </span>
    </label>

    <div id="epf-fields" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label for="epf_number" class="{{ $label }}">{{ __('EPF No.') }}</label>
            <input type="text" name="epf_number" id="epf_number" maxlength="30" value="{{ old('epf_number', $worker?->epf_number) }}" class="{{ $field }}">
            @error('epf_number') <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
        </div>
        @foreach ([
            'epf_employee_rate' => [__('EPF – employee'), 8],
            'epf_employer_rate' => [__('EPF – employer'), 12],
            'etf_rate' => [__('ETF – employer'), 3],
        ] as $name => [$text, $default])
            <div>
                <label for="{{ $name }}" class="{{ $label }}">{{ $text }}</label>
                <div class="relative">
                    <input type="number" name="{{ $name }}" id="{{ $name }}" step="0.01" min="0" max="100" inputmode="decimal"
                           value="{{ old($name, $worker ? (float) $worker->{$name} : $default) }}" class="{{ $field }} pr-8">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">%</span>
                </div>
                @error($name) <p class="text-xs text-accent mt-1">{{ $message }}</p> @enderror
            </div>
        @endforeach
        <p class="sm:col-span-4 text-[11px] text-gray-400 dark:text-gray-500 -mt-2">{{ __('The employee EPF share is taken from their pay. Employer EPF and ETF are paid by the business on top.') }}</p>
    </div>
</div>

<div>
    <label for="address" class="{{ $label }}">{{ __('Address') }}</label>
    <input type="text" name="address" id="address" maxlength="255" value="{{ old('address', $worker?->address) }}" class="{{ $field }}">
</div>

<div>
    <label for="notes" class="{{ $label }}">{{ __('Notes') }}</label>
    <textarea name="notes" id="notes" rows="2" maxlength="1000" class="{{ $field }} resize-none">{{ old('notes', $worker?->notes) }}</textarea>
</div>

@if ($worker)
    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', $worker->is_active) ? 'checked' : '' }}>
        {{ __('Active') }}
    </label>
@endif

<script>
    function togglePayFields() {
        const type = document.querySelector('input[name=pay_type]:checked')?.value;
        document.querySelectorAll('[data-pay]').forEach((el) => el.classList.toggle('hidden', el.dataset.pay !== type));
    }
    togglePayFields();

    function toggleEpfFields() {
        document.getElementById('epf-fields').classList.toggle('hidden', !document.getElementById('epf_enabled').checked);
    }
    toggleEpfFields();
</script>
