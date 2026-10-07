@extends('layouts.frontend')

@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Salary & Work'),
        'crumbs' => [__('Salary & Work') => null],
        'subtitle' => now()->translatedFormat('l, d F Y'),
        'actions' => [
            ['url' => route('help'), 'label' => __('Help'), 'icon' => \App\Support\Help::ICON],
        ],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        {{-- ── step 1: work, step 2: pay ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.work.create'),
                'label' => __('Enter Today\'s Work'),
                'hint' => __('What each worker did today'),
                'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                'primary' => true,
            ])
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.salary.create'),
                'label' => __('Pay Salary'),
                'hint' => __('Pay a worker & print payslip'),
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            ])
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.salary'),
                'label' => __('Balances & Payments'),
                'hint' => __('Who is owed & past payslips'),
                'icon' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6M9 16h3',
            ])
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.work'),
                'label' => __('Daily Work Sheet'),
                'hint' => __('See & fix past days'),
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            ])
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.production'),
                'label' => __('Production Report'),
                'hint' => __('Made vs dispatched'),
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            ])
            @can('manage-pricing')
                @include('frontend.componenet.hubtile', [
                    'url' => route('products.activities'),
                    'label' => __('Work Rates'),
                    'hint' => __('Pay for each kind of work'),
                    'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                ])
            @endcan
        </div>

        {{-- ── money summary ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-6">
            <div class="col-span-2 lg:col-span-1 rounded-2xl bg-surface border-2 border-amber-300 dark:border-amber-500/40 p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-sans">{{ __('Unpaid Wages') }}</p>
                <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white tracking-tight mt-1">{{ Money::format($stats['unpaid']) }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans mt-2">{{ __('Earned but not yet paid') }}</p>
            </div>
            @foreach ([
                ['label' => __('Worked Today'), 'value' => $stats['present_today'].' / '.$stats['workers']],
                ['label' => __('Earned Today'), 'value' => Money::format($stats['earned_today'])],
                ['label' => __('Paid This Month'), 'value' => Money::format($stats['paid_month'])],
            ] as $stat)
                <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm {{ $loop->last ? 'col-span-2 sm:col-span-1' : '' }}">
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ $stat['label'] }}</p>
                    <p class="font-heading font-semibold text-lg sm:text-xl text-gray-900 dark:text-white tracking-tight mt-1">{{ $stat['value'] }}</p>
                </div>
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
