{{-- ────────────────────────── EXPENSES HUB BODY ────────────────────────── --}}
@use('App\Models\Expense')
@php
    $monthTotal = $stats['month'];
@endphp
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.expenses.partials.alerts')

    {{-- ── summary ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        {{-- this month --}}
        <div class="col-span-2 lg:col-span-1 rounded-2xl bg-brand text-white p-4 sm:p-5 shadow-sm">
            <p class="text-xs text-white/70 font-sans">{{ __('This Month') }}</p>
            <p class="font-heading font-semibold text-2xl tracking-tight mt-1">{{ Expense::money($stats['month']) }}</p>
            <div class="flex items-center gap-2 mt-2 text-[11px] font-sans">
                <span class="text-white/70">{{ trans_choice(':count expense|:count expenses', $stats['month_count'], ['count' => $stats['month_count']]) }}</span>
                @if (! is_null($stats['change']))
                    <span class="px-1.5 py-0.5 rounded-full {{ $stats['change'] > 0 ? 'bg-red-400/25 text-red-100' : 'bg-green-400/25 text-green-100' }}">
                        {{ $stats['change'] > 0 ? '▲' : '▼' }} {{ abs($stats['change']) }}% {{ __('vs last month') }}
                    </span>
                @endif
            </div>
        </div>

        @foreach ([
            ['label' => __('Today'), 'value' => $stats['today']],
            ['label' => __('This Week'), 'value' => $stats['week']],
            ['label' => __('Last Month'), 'value' => $stats['last_month']],
        ] as $stat)
            <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm {{ $loop->last ? 'col-span-2 sm:col-span-1' : '' }}">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ $stat['label'] }}</p>
                <p class="font-heading font-semibold text-lg sm:text-xl text-gray-900 dark:text-white tracking-tight mt-1">{{ Expense::money($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── quick actions ── --}}
    <div class="grid grid-cols-3 gap-3 sm:gap-4 mt-4">
        @foreach ([
            ['route' => route('expenses.create'), 'label' => __('Add Expense'), 'hint' => __('Record a payment'), 'icon' => 'M12 4v16m8-8H4'],
            ['route' => route('expenses.list'), 'label' => __('Expense Records'), 'hint' => __('Search & filter'), 'icon' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6M9 16h3'],
            ['route' => route('expenses.categories'), 'label' => __('Expense Types'), 'hint' => __('Add & manage types'), 'icon' => 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
        ] as $action)
            <a href="{{ $action['route'] }}" class="flex flex-col items-center justify-center gap-2 h-28 sm:h-32 rounded-2xl bg-surface border border-subtle shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 text-center px-2">
                <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/>
                    </svg>
                </span>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-[13px] sm:text-sm tracking-tight text-gray-900 dark:text-white">{{ $action['label'] }}</span>
                    <span class="hidden sm:block text-[11px] text-gray-400 dark:text-gray-500">{{ $action['hint'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    {{-- ── spending by type ── --}}
    <div class="flex items-center justify-between mt-8">
        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('Spending by Type') }}</h2>
        <a href="{{ route('expenses.categories') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand font-sans">{{ __('Manage types') }} →</a>
    </div>
    <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach ($categories as $category)
            @php $share = $monthTotal > 0 ? round(((float) $category->month_total / $monthTotal) * 100) : 0; @endphp
            <a href="{{ route('expenses.list', ['category' => $category->id, 'from' => $monthRange[0], 'to' => $monthRange[1], 'branch_id' => $branchId]) }}"
               class="group flex flex-col rounded-2xl bg-surface border border-subtle p-4 shadow-sm hover:shadow-md active:scale-[0.98] transition-all duration-200">
                <div class="flex items-start justify-between gap-2">
                    @include('frontend.expenses.partials.icon', ['category' => $category, 'size' => 'md'])
                    @if ($category->month_count > 0)
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">{{ $share }}%</span>
                    @endif
                </div>
                <p class="font-medium text-sm text-gray-900 dark:text-white tracking-tight mt-3 truncate">{{ $category->name }}</p>
                <p class="font-heading font-semibold text-base {{ $category->month_count > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-gray-600' }} mt-0.5">
                    {{ Expense::money($category->month_total) }}
                </p>
                <div class="h-1.5 rounded-full bg-gray-100 dark:bg-white/10 mt-3 overflow-hidden">
                    <div class="h-full rounded-full {{ $category->palette()['solid'] }}" style="width: {{ $share }}%"></div>
                </div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans mt-2">
                    {{ trans_choice(':count entry|:count entries', $category->month_count, ['count' => $category->month_count]) }}
                </p>
            </a>
        @endforeach

        {{-- add type --}}
        <a href="{{ route('expenses.categories') }}#add-type"
           class="flex flex-col items-center justify-center gap-2 min-h-[10rem] rounded-2xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] text-gray-400 dark:text-gray-500 hover:text-brand hover:border-gray-400 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-sm font-medium">{{ __('New Expense Type') }}</span>
        </a>
    </div>

    {{-- ── recent expenses ── --}}
    <div class="flex items-center justify-between mt-8">
        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('Recent Expenses') }}</h2>
        <a href="{{ route('expenses.list') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand font-sans">{{ __('View all') }} →</a>
    </div>
    <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

    @if ($recentExpenses->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-14 rounded-2xl bg-surface border border-subtle">
            <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No expenses recorded yet') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('Tap "Add Expense" to record your first one.') }}</p>
        </div>
    @else
        <div class="rounded-2xl bg-surface border border-subtle shadow-sm divide-y divide-gray-100 dark:divide-[#1c3350] overflow-hidden">
            @foreach ($recentExpenses as $expense)
                <a href="{{ route('expenses.edit', $expense) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-alt transition-colors">
                    @include('frontend.expenses.partials.icon', ['category' => $expense->category, 'size' => 'sm'])
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $expense->title }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans truncate">
                            {{ $expense->expense_date->format('d M Y') }} · {{ $expense->category->name }} · {{ $expense->branch->name ?? '—' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Expense::money($expense->amount) }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">{{ $expense->paymentMethodLabel() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</main>
