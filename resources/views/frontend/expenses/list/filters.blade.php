{{-- ────────────────────────── EXPENSE FILTERS ────────────────────────── --}}
@php
    $field = 'w-full px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $now = now();
    $presets = [
        __('Today') => [$now->toDateString(), $now->toDateString()],
        __('This Week') => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
        __('This Month') => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        __('Last Month') => [$now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()],
    ];
    $advancedOpen = filled($filters['category'] ?? null) || filled($filters['branch_id'] ?? null) || filled($filters['payment_method'] ?? null) || filled($filters['from'] ?? null) || filled($filters['to'] ?? null);
@endphp
<section class="px-5 pt-4 max-w-[1600px] mx-auto">
    <form action="{{ route('expenses.list') }}" method="GET" class="space-y-3">

        {{-- search row --}}
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="{{ __('Search by description, code, paid to or reference') }}"
                       class="{{ $field }} pl-10">
            </div>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand text-white text-sm font-medium active:scale-95 transition-transform">
                {{ __('Search') }}
            </button>

            @if ($hasFilters)
                <a href="{{ route('expenses.list') }}"
                   class="px-4 py-2.5 rounded-xl bg-surface-alt border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium active:scale-95 transition-transform">
                    {{ __('Clear') }}
                </a>
            @endif
        </div>

        {{-- date presets --}}
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($presets as $label => [$from, $to])
                @php $active = ($filters['from'] ?? null) === $from && ($filters['to'] ?? null) === $to; @endphp
                <a href="{{ route('expenses.list', array_merge($filters, ['from' => $from, 'to' => $to])) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors
                          {{ $active ? 'bg-brand text-white border-transparent' : 'bg-surface border-gray-300 dark:border-[#2a4a70] text-gray-600 dark:text-gray-300 hover:border-gray-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- advanced filters --}}
        <details class="group rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70]" {{ $advancedOpen ? 'open' : '' }}>
            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none list-none text-sm font-medium text-gray-700 dark:text-gray-200">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
                    </svg>
                    {{ __('More filters') }}
                </span>
                <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 px-4 pb-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Expense Type') }}</label>
                    <select name="category" class="{{ $field }}">
                        <option value="">{{ __('All types') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) ($filters['category'] ?? '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Branch') }}</label>
                    <select name="branch_id" class="{{ $field }}">
                        <option value="">{{ __('All branches') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string) ($filters['branch_id'] ?? '') === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Payment Method') }}</label>
                    <select name="payment_method" class="{{ $field }}">
                        <option value="">{{ __('All methods') }}</option>
                        @foreach (\App\Models\Expense::PAYMENT_METHODS as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['payment_method'] ?? '') === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('From') }}</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('To') }}</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="{{ $field }}">
                </div>
                <div class="sm:col-span-2 lg:col-span-5 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand text-white text-sm font-medium active:scale-95 transition-transform">
                        {{ __('Apply Filters') }}
                    </button>
                </div>
            </div>
        </details>
    </form>
</section>
