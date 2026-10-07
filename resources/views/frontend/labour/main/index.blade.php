@extends('layouts.frontend')

@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Labour & Salary'),
        'crumbs' => [__('Labour') => null],
        'subtitle' => now()->translatedFormat('l, d F Y'),
        'actions' => [
            ['url' => route('help'), 'label' => __('Help'), 'icon' => \App\Support\Help::ICON],
            ['url' => route('labour.work.create'), 'label' => __('Enter Today\'s Work'), 'primary' => true],
        ],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

        @include('frontend.componenet.alerts')

        {{-- ── summary ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="col-span-2 lg:col-span-1 rounded-2xl bg-brand text-white p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-white/70 font-sans">{{ __('Unpaid Wages') }}</p>
                <p class="font-heading font-semibold text-2xl tracking-tight mt-1">{{ Money::format($stats['unpaid']) }}</p>
                <p class="text-[11px] text-white/70 font-sans mt-2">{{ __('Earned but not yet paid') }}</p>
            </div>
            @foreach ([
                ['label' => __('Working Today'), 'value' => $stats['present_today'].' / '.$stats['workers']],
                ['label' => __('Earned Today'), 'value' => Money::format($stats['earned_today'])],
                ['label' => __('Paid This Month'), 'value' => Money::format($stats['paid_month'])],
            ] as $stat)
                <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm {{ $loop->last ? 'col-span-2 sm:col-span-1' : '' }}">
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ $stat['label'] }}</p>
                    <p class="font-heading font-semibold text-lg sm:text-xl text-gray-900 dark:text-white tracking-tight mt-1">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── quick actions ── --}}
        @php
            $labourActions = [
                ['route' => route('attendance'), 'label' => __('Check In / Out'), 'hint' => __(':count working now', ['count' => $stats['checked_in']]), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            if (auth()->user()->can('manage-attendance')) {
                $labourActions[] = ['route' => route('attendance.board'), 'label' => __('Attendance Board'), 'hint' => __('Who is working'), 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
            }
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 {{ count($labourActions) > 1 ? 'lg:grid-cols-6' : 'lg:grid-cols-5' }} gap-3 sm:gap-4 mt-4">
            @foreach (array_merge($labourActions, [
                ['route' => route('labour.work'), 'label' => __('Daily Work'), 'hint' => __('Attendance & work done'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 8l2 2 4-4'],
                ['route' => route('labour.salary'), 'label' => __('Pay Salary'), 'hint' => __('Balances & payslips'), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => route('labour.workers'), 'label' => __('Workers'), 'hint' => __('Labourers & staff'), 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87'],
                ['route' => route('labour.production'), 'label' => __('Production Report'), 'hint' => __('Made vs dispatched'), 'icon' => 'M9 19V6m6 13V10M3 19V12m18 7V3'],
            ]) as $action)
                <a href="{{ $action['route'] }}" class="flex flex-col items-center justify-center gap-2 h-28 sm:h-32 rounded-2xl bg-surface border border-subtle shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 text-center px-2">
                    <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-surface-alt flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="flex flex-col items-center gap-0.5">
                        <span class="font-medium text-[13px] sm:text-sm tracking-tight text-gray-900 dark:text-white">{{ $action['label'] }}</span>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $action['hint'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>

        {{-- ── today's production ── --}}
        <div class="flex items-center justify-between mt-8">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('Today\'s Production') }}</h2>
            <a href="{{ route('labour.production') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand font-sans">{{ __('Full report') }} →</a>
        </div>
        <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

        @if ($todayTotals->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-14 rounded-2xl bg-surface border border-subtle">
                <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No work recorded today yet') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('At day end, enter what each worker did.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($todayTotals as $row)
                    <div class="rounded-2xl bg-surface border border-subtle shadow-sm p-4">
                        <p class="font-medium text-gray-900 dark:text-white truncate">{{ $row->name }}</p>
                        <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                            <div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ __('Produced') }}</p>
                                <p class="font-heading font-semibold text-gray-900 dark:text-white">{{ Money::qty($row->produced) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ __('Dispatched') }}</p>
                                <p class="font-heading font-semibold text-gray-900 dark:text-white">{{ Money::qty($row->dispatched) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ __('Difference') }}</p>
                                <p class="font-heading font-semibold {{ $row->produced - $row->dispatched < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ Money::qty($row->produced - $row->dispatched) }}</p>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 text-center mt-2">{{ __('in :unit', ['unit' => __($row->unit)]) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection
