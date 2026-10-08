@extends('layouts.frontend')

@use('App\Support\Money')

@section('content')
    @php $day = \Illuminate\Support\Carbon::parse($date); @endphp
    @include('frontend.componenet.pagehero', [
        'title' => __('Daily Work'),
        'crumbs' => [__('Salary & Work') => route('salary'), __('Daily Work') => null],
        'subtitle' => $day->translatedFormat('l, d F Y'),
        'actions' => [['url' => route('labour.work.create', ['date' => $date]), 'label' => __('Enter Work'), 'primary' => true]],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        @php $filterField = 'px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40'; @endphp
        <form action="{{ route('labour.work') }}" method="GET" class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('labour.work', ['date' => $day->copy()->subDay()->toDateString(), 'branch_id' => $branchId]) }}" class="{{ $filterField }}" title="{{ __('Previous day') }}">←</a>
            <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" onchange="this.form.submit()" class="{{ $filterField }}">
            @if ($day->lt(today()))
                <a href="{{ route('labour.work', ['date' => $day->copy()->addDay()->toDateString(), 'branch_id' => $branchId]) }}" class="{{ $filterField }}" title="{{ __('Next day') }}">→</a>
            @endif
            <select name="branch_id" onchange="this.form.submit()" class="{{ $filterField }}">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </form>

        {{-- ── summary ── --}}
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl bg-surface border border-subtle p-4 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Present') }}</p>
                <p class="font-heading font-semibold text-lg sm:text-xl text-gray-900 dark:text-white mt-1">{{ $summary['present'] }}</p>
            </div>
            <div class="rounded-2xl bg-surface border border-subtle p-4 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Entered') }}</p>
                <p class="font-heading font-semibold text-lg sm:text-xl text-gray-900 dark:text-white mt-1">{{ $summary['recorded'] }} / {{ $workers->count() }}</p>
            </div>
            <div class="rounded-2xl bg-brand text-white p-4 shadow-sm">
                <p class="text-xs text-white/70 font-sans">{{ __('Wages Earned') }}</p>
                <p class="font-heading font-semibold text-lg sm:text-xl mt-1">{{ Money::format($summary['earnings']) }}</p>
            </div>
        </div>

        {{-- ── workers ── --}}
        <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                            <th class="px-4 py-3 font-medium">{{ __('Worker') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Attendance') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Work Done') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Earned') }}</th>
                            <th class="px-4 py-3 font-medium text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                        @forelse ($workers as $worker)
                            @php
                                $entry = $entries->get($worker->id);
                                $away = $entry ? null : $elsewhere->get($worker->id);
                            @endphp
                            <tr class="hover:bg-surface-alt transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $worker->name }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $worker->code }} · {{ $worker->payTypeLabel() }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($entry)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                            {{ match ($entry->attendance) {
                                                'present' => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400',
                                                'half_day' => 'bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300',
                                                default => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400',
                                            } }}">{{ $entry->attendanceLabel() }}</span>
                                    @elseif ($away)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#2f6fb8]/10 text-[#2f6fb8] dark:bg-[#4a8ad4]/15 dark:text-[#8bb8ea]">
                                            {{ __('Worked at :branch', ['branch' => $away->branch?->name]) }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-gray-400">{{ __('Not entered') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 min-w-[14rem]">
                                    @if ($entry && $entry->items->isNotEmpty())
                                        <ul class="space-y-0.5 text-gray-700 dark:text-gray-200">
                                            @foreach ($entry->items as $item)
                                                <li>
                                                    <span class="font-medium">{{ Money::qty($item->quantity) }}</span>
                                                    {{ $item->product->name }}
                                                    <span class="text-gray-400">· {{ $item->activity->label() }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif ($entry)
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if ($entry)
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ Money::format($entry->total_earnings) }}</p>
                                        <p class="text-[11px] {{ $entry->isPaid() ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">{{ $entry->isPaid() ? __('Paid') : __('Unpaid') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if ($entry)
                                        <a href="{{ route('labour.work.edit', $entry) }}"
                                           class="inline-flex px-3 py-1.5 rounded-lg text-xs font-medium bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200">{{ $entry->isPaid() ? __('View') : __('Edit') }}</a>
                                    @elseif ($away)
                                        <a href="{{ route('labour.work', ['date' => $date, 'branch_id' => $away->branch_id]) }}"
                                           class="inline-flex px-3 py-1.5 rounded-lg text-xs font-medium bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200">{{ __('View') }}</a>
                                    @else
                                        <a href="{{ route('labour.work.create', array_filter(['worker' => $worker->id, 'date' => $date, 'branch_id' => $branchId])) }}"
                                           class="inline-flex px-3 py-1.5 rounded-lg text-xs font-medium bg-brand text-white">{{ __('Enter') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">
                                    {{ __('No workers for this branch.') }}
                                    <a href="{{ route('labour.workers.create') }}" class="text-brand underline">{{ __('Add a worker') }}</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── production for the day ── --}}
        @if ($productTotals->isNotEmpty())
            <div class="flex items-center justify-between mt-8">
                <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('Production on this Day') }}</h2>
                <a href="{{ route('labour.production', ['from' => $date, 'to' => $date, 'branch_id' => $branchId]) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand font-sans">{{ __('Details') }} →</a>
            </div>
            <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($productTotals as $row)
                    <div class="rounded-2xl bg-surface border border-subtle shadow-sm p-4">
                        <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $row['product']->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Produced') }} <span class="font-semibold text-gray-900 dark:text-white">{{ Money::qty($row['produced']) }}</span></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Dispatched') }} <span class="font-semibold text-gray-900 dark:text-white">{{ Money::qty($row['dispatched']) }}</span></p>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection
