{{-- Shared expense form fields. Expects $categories, $branches; optional $expense, $selectedCategory. --}}
@php
    $expense = $expense ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    $currentCategory = old('expense_category_id', $expense?->expense_category_id ?? $selectedCategory ?? null);
    $currentMethod = old('payment_method', $expense?->payment_method ?? 'cash');
    $currentBranch = old('branch_id', $expense?->branch_id ?? auth()->user()?->branch_id);
@endphp

{{-- ── expense type ── --}}
<div>
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Expense Type') }} <span class="text-accent">*</span></p>
        <a href="{{ route('expenses.categories') }}#add-type" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand">+ {{ __('New type') }}</a>
    </div>

    @if ($categories->isEmpty())
        <div class="px-4 py-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No expense types yet.') }}
            <a href="{{ route('expenses.categories') }}#add-type" class="text-brand font-medium underline">{{ __('Create one first') }}</a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
            @foreach ($categories as $category)
                <label class="relative cursor-pointer">
                    <input type="radio" name="expense_category_id" value="{{ $category->id }}" class="sr-only peer" required
                           {{ (string) $currentCategory === (string) $category->id ? 'checked' : '' }}>
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl border-2 border-gray-200 dark:border-[#2a4a70] bg-surface transition-all
                                peer-checked:border-[#004080] dark:peer-checked:border-blue-400 peer-checked:bg-blue-50/60 dark:peer-checked:bg-blue-500/10
                                hover:border-gray-300 dark:hover:border-[#355a85]">
                        @include('frontend.expenses.partials.icon', ['category' => $category, 'size' => 'sm'])
                        <span class="text-[13px] font-medium text-gray-800 dark:text-gray-100 leading-tight line-clamp-2">{{ $category->name }}</span>
                    </div>
                    <span class="absolute top-1.5 right-1.5 hidden peer-checked:flex w-4 h-4 rounded-full bg-[#004080] dark:bg-blue-400 text-white items-center justify-center">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </label>
            @endforeach
        </div>
    @endif
    @error('expense_category_id')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- ── amount & date ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="amount" class="{{ $label }}">{{ __('Amount') }} <span class="text-accent">*</span></label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rs.</span>
            <input type="number" name="amount" id="amount" step="0.01" min="0.01" inputmode="decimal" required
                   value="{{ old('amount', $expense?->amount) }}" placeholder="0.00"
                   class="{{ $field }} pl-12 text-lg font-semibold">
        </div>
        @error('amount')
            <p class="text-xs text-accent mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="expense_date" class="{{ $label }}">{{ __('Date') }} <span class="text-accent">*</span></label>
        <input type="date" name="expense_date" id="expense_date" required max="{{ now()->toDateString() }}"
               value="{{ old('expense_date', $expense?->expense_date?->toDateString() ?? now()->toDateString()) }}"
               class="{{ $field }} py-3">
        @error('expense_date')
            <p class="text-xs text-accent mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- ── description ── --}}
<div>
    <label for="title" class="{{ $label }}">{{ __('Description') }}</label>
    <input type="text" name="title" id="title" maxlength="255"
           value="{{ old('title', $expense?->title) }}"
           placeholder="{{ __('e.g. October shop rent (optional — defaults to the type name)') }}"
           class="{{ $field }}">
    @error('title')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- ── payment method ── --}}
<div>
    <p class="{{ $label }}">{{ __('Payment Method') }} <span class="text-accent">*</span></p>
    <div class="flex flex-wrap gap-2">
        @foreach (\App\Models\Expense::PAYMENT_METHODS as $value => $methodLabel)
            <label class="cursor-pointer">
                <input type="radio" name="payment_method" value="{{ $value }}" class="sr-only peer" {{ $currentMethod === $value ? 'checked' : '' }}>
                <span class="inline-block px-3.5 py-2 rounded-full text-sm font-medium border border-gray-300 dark:border-[#2a4a70] text-gray-600 dark:text-gray-300 bg-surface transition-colors
                             peer-checked:bg-[#004080] peer-checked:border-[#004080] peer-checked:text-white">
                    {{ __($methodLabel) }}
                </span>
            </label>
        @endforeach
    </div>
    @error('payment_method')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- ── branch, paid to, reference ── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label for="branch_id" class="{{ $label }}">{{ __('Branch') }} <span class="text-accent">*</span></label>
        <select name="branch_id" id="branch_id" required class="{{ $field }}">
            <option value="" disabled {{ $currentBranch ? '' : 'selected' }}>{{ __('Select a branch') }}</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" {{ (string) $currentBranch === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')
            <p class="text-xs text-accent mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="paid_to" class="{{ $label }}">{{ __('Paid To') }}</label>
        <input type="text" name="paid_to" id="paid_to" maxlength="255"
               value="{{ old('paid_to', $expense?->paid_to) }}" placeholder="{{ __('e.g. CEB, landlord, shop name') }}"
               class="{{ $field }}">
        @error('paid_to')
            <p class="text-xs text-accent mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="reference_no" class="{{ $label }}">{{ __('Bill / Reference No.') }}</label>
        <input type="text" name="reference_no" id="reference_no" maxlength="100"
               value="{{ old('reference_no', $expense?->reference_no) }}" placeholder="{{ __('Optional') }}"
               class="{{ $field }}">
        @error('reference_no')
            <p class="text-xs text-accent mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- ── notes ── --}}
<div>
    <label for="notes" class="{{ $label }}">{{ __('Notes') }}</label>
    <textarea name="notes" id="notes" rows="3" maxlength="1000"
              placeholder="{{ __('Any extra details (optional)') }}"
              class="{{ $field }} resize-none">{{ old('notes', $expense?->notes) }}</textarea>
    @error('notes')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- ── receipt ── --}}
<div>
    <p class="{{ $label }}">{{ __('Receipt / Bill Photo') }}</p>

    @if ($expense?->receipt_path)
        <div class="flex items-center gap-3 mb-2 px-3 py-2.5 rounded-xl bg-surface-alt border border-gray-300 dark:border-[#2a4a70]">
            @if ($expense->receiptIsImage())
                <img src="{{ $expense->receiptUrl() }}" alt="{{ __('Receipt') }}" class="w-12 h-12 rounded-lg object-cover">
            @else
                <span class="w-12 h-12 rounded-lg bg-red-50 dark:bg-red-500/15 text-red-600 dark:text-red-300 flex items-center justify-center text-xs font-bold">PDF</span>
            @endif
            <div class="flex-1 min-w-0">
                <a href="{{ $expense->receiptUrl() }}" target="_blank" class="text-sm font-medium text-brand underline">{{ __('View current receipt') }}</a>
                <label class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                    <input type="checkbox" name="remove_receipt" value="1" class="rounded"> {{ __('Remove receipt') }}
                </label>
            </div>
        </div>
    @endif

    <label for="receipt" class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] cursor-pointer hover:border-gray-400 dark:hover:border-[#355a85] transition-colors">
        <svg class="w-6 h-6 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9zM15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="flex-1 min-w-0">
            <span id="receipt-name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 truncate"
                  data-default="{{ $expense?->receipt_path ? __('Replace receipt') : __('Attach a photo or PDF') }}">
                {{ $expense?->receipt_path ? __('Replace receipt') : __('Attach a photo or PDF') }}
            </span>
            <span class="block text-[11px] text-gray-400 dark:text-gray-500">{{ __('JPG, PNG, WEBP or PDF · max 4 MB') }}</span>
        </span>
        <input type="file" name="receipt" id="receipt" accept="image/*,application/pdf" class="sr-only"
               onchange="const n = document.getElementById('receipt-name'); n.textContent = this.files[0] ? this.files[0].name : n.dataset.default">
    </label>
    @error('receipt')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>
