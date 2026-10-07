@extends('layouts.frontend')

@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Production Report'),
        'crumbs' => [__('Salary & Work') => route('salary'), __('Production') => null],
        'subtitle' => __('Units produced vs units dispatched (loaded), from daily work entries'),
        'actions' => [['url' => route('help').'#stock', 'label' => __('Help'), 'icon' => \App\Support\Help::ICON]],
    ])

    @php
        $filterField = 'px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
        $productNames = $products->keyBy('id');
    @endphp

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        <form action="{{ route('labour.production') }}" method="GET" class="flex flex-wrap items-end gap-2 mb-4 print:hidden">
            <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('From') }}
                <input type="date" name="from" value="{{ $from }}" max="{{ now()->toDateString() }}" class="{{ $filterField }} block mt-1">
            </label>
            <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('To') }}
                <input type="date" name="to" value="{{ $to }}" max="{{ now()->toDateString() }}" class="{{ $filterField }} block mt-1">
            </label>
            <select name="branch_id" class="{{ $filterField }}">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand text-white text-sm font-medium">{{ __('Show') }}</button>
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-[#2a4a70] text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Print') }}</button>
        </form>

        @if ($report->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
                <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No work recorded in this period') }}</p>
            </div>
        @else
            {{-- ── per product ── --}}
            <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm font-sans">
                        <thead>
                            <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                                <th class="px-4 py-3 font-medium">{{ __('Product') }}</th>
                                @foreach ($activities as $activity)
                                    <th class="px-4 py-3 font-medium text-right">{{ $activity->label() }}</th>
                                @endforeach
                                <th class="px-4 py-3 font-medium text-right bg-green-50/60 dark:bg-green-900/10">{{ __('Produced') }}</th>
                                <th class="px-4 py-3 font-medium text-right bg-purple-50/60 dark:bg-purple-900/10">{{ __('Dispatched') }}</th>
                                <th class="px-4 py-3 font-medium text-right">{{ __('Difference') }}</th>
                                <th class="px-4 py-3 font-medium text-right">{{ __('Labour Paid') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                            @foreach ($report as $row)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $row['product']->name }}</p>
                                        <p class="text-[11px] text-gray-400">{{ __('in :unit', ['unit' => __($row['product']->unit)]) }}</p>
                                    </td>
                                    @foreach ($activities as $activity)
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ Money::qty($row['activities'][$activity->id]) }}</td>
                                    @endforeach
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white bg-green-50/60 dark:bg-green-900/10">{{ Money::qty($row['produced']) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white bg-purple-50/60 dark:bg-purple-900/10">{{ Money::qty($row['dispatched']) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $row['balance'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $row['balance'] > 0 ? '+' : '' }}{{ Money::qty($row['balance']) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ Money::format($row['labour_paid']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-surface-alt border-t border-gray-300 dark:border-[#2a4a70] font-semibold text-gray-900 dark:text-white">
                                <td class="px-4 py-3">{{ __('Total') }}</td>
                                <td colspan="{{ $activities->count() + 3 }}"></td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">{{ Money::format($report->sum('labour_paid')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans mt-2">
                {{ __('Positive difference = stock left in the yard. Negative = more dispatched than produced in this period (check entries or opening stock).') }}
            </p>

            {{-- ── day by day ── --}}
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight mt-8">{{ __('Day by Day') }}</h2>
            <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

            <div class="space-y-3">
                @foreach ($daily as $day => $rows)
                    <div class="rounded-2xl bg-surface border border-subtle shadow-sm overflow-hidden">
                        <a href="{{ route('labour.work', ['date' => $day, 'branch_id' => $branchId]) }}"
                           class="flex items-center justify-between px-4 py-2.5 bg-surface-alt text-sm font-medium text-gray-900 dark:text-white hover:text-brand">
                            {{ \Illuminate\Support\Carbon::parse($day)->translatedFormat('l, d M Y') }}
                            <span class="text-xs text-gray-400">{{ __('View day') }} →</span>
                        </a>
                        <table class="w-full text-sm font-sans">
                            <tbody class="divide-y divide-gray-100 dark:divide-[#1c3350]">
                                @foreach ($rows as $row)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $productNames[$row->product_id]->name ?? '—' }}</td>
                                        <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('Produced') }} <span class="font-semibold text-gray-900 dark:text-white">{{ Money::qty($row->produced) }}</span></td>
                                        <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('Dispatched') }} <span class="font-semibold text-gray-900 dark:text-white">{{ Money::qty($row->dispatched) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection
