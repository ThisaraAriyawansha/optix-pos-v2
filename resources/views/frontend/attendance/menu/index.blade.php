@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Attendance'),
        'crumbs' => [__('Attendance') => null],
        'subtitle' => now()->translatedFormat('l, d F Y'),
        'actions' => [
            ['url' => route('help').'#attendance', 'label' => __('Help'), 'icon' => \App\Support\Help::ICON],
        ],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        @php $canManage = auth()->user()->can('manage-attendance'); @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            @include('frontend.componenet.hubtile', [
                'url' => route('attendance'),
                'label' => __('Check In / Check Out'),
                'hint' => __('Mark who came and who left'),
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'primary' => true,
                'badge' => $stats['working_now'],
            ])

            @if ($canManage)
                @include('frontend.componenet.hubtile', [
                    'url' => route('attendance.board'),
                    'label' => __('Live Board'),
                    'hint' => __('Who is working now'),
                    'icon' => 'M9 19V6m6 13V10M3 19V12m18 7V3',
                ])
                @include('frontend.componenet.hubtile', [
                    'url' => route('attendance.report'),
                    'label' => __('Attendance History'),
                    'hint' => __('Days & hours worked'),
                    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                ])
                @include('frontend.componenet.hubtile', [
                    'url' => route('attendance.people'),
                    'label' => __('Who Checks In'),
                    'hint' => __('Choose people & fingerprint'),
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 8l2 2 4-4',
                ])
            @endif
        </div>

        {{-- ── today ── --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mt-4">
            <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Working Now') }}</p>
                <p class="font-heading font-semibold text-2xl text-green-600 dark:text-green-400 tracking-tight mt-1">{{ $stats['working_now'] }}</p>
            </div>
            <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Came Today') }}</p>
                <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white tracking-tight mt-1">{{ $stats['came_today'] }}</p>
            </div>
        </div>
    </main>
@endsection
