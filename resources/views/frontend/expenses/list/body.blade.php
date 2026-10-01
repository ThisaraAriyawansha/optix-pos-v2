{{-- ────────────────────────── EXPENSE RECORDS BODY ────────────────────────── --}}
@use('App\Models\Expense')
<main class="px-5 pt-5 pb-28 max-w-[1600px] mx-auto">

    @include('frontend.expenses.partials.alerts')

    {{-- totals bar --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3 px-4 py-3 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70]">
        <p class="text-sm text-gray-500 dark:text-gray-400 font-sans">
            {{ trans_choice(':count record|:count records', $expenses->total(), ['count' => $expenses->total()]) }}
            @if ($hasFilters) <span class="text-gray-400">· {{ __('filtered') }}</span> @endif
        </p>
        <p class="text-sm font-sans text-gray-500 dark:text-gray-400">
            {{ __('Total') }}
            <span class="font-heading font-semibold text-lg text-gray-900 dark:text-white ml-1">{{ Expense::money($total) }}</span>
        </p>
    </div>

    @if ($expenses->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
            <div class="w-12 h-12 rounded-full bg-surface-alt flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6M9 16h3"/>
                </svg>
            </div>
            <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">
                {{ $hasFilters ? __('No expenses match these filters') : __('No expenses yet') }}
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
                {{ $hasFilters ? __('Try a different search or date range.') : __('Add your first expense to get started.') }}
            </p>
        </div>
    @else
        <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                            <th class="px-4 py-3 font-medium">{{ __('Date') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Type') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Description') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Branch') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Payment') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Amount') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Edit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                        @foreach ($expenses as $expense)
                            <tr onclick="window.location='{{ route('expenses.edit', $expense) }}'"
                                class="cursor-pointer hover:bg-surface-alt transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $expense->expense_date->format('d M Y') }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $expense->expense_code }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @include('frontend.expenses.partials.icon', ['category' => $expense->category, 'size' => 'sm'])
                                        <span class="text-gray-700 dark:text-gray-200">{{ $expense->category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 min-w-[12rem]">
                                    <p class="text-gray-700 dark:text-gray-200 flex items-center gap-1.5">
                                        {{ $expense->title }}
                                        @if ($expense->receipt_path)
                                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-label="{{ __('Has receipt') }}">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                        @endif
                                    </p>
                                    @if ($expense->paid_to || $expense->reference_no)
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500">
                                            {{ collect([$expense->paid_to, $expense->reference_no ? __('Ref').': '.$expense->reference_no : null])->filter()->implode(' · ') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $expense->branch->name ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300">
                                        {{ $expense->paymentMethodLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ Expense::money($expense->amount) }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('expenses.edit', $expense) }}" onclick="event.stopPropagation()"
                                       class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($expenses->hasPages())
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        @endif
    @endif
</main>
