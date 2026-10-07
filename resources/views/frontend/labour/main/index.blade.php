@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Workers'),
        'crumbs' => [__('Workers') => null],
        'subtitle' => __('Register labourers and keep their details'),
        'actions' => [
            ['url' => route('help'), 'label' => __('Help'), 'icon' => \App\Support\Help::ICON],
        ],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.workers.create'),
                'label' => __('Register New Worker'),
                'hint' => __('Add a new labourer'),
                'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                'primary' => true,
            ])
            @include('frontend.componenet.hubtile', [
                'url' => route('labour.workers'),
                'label' => __('All Workers'),
                'hint' => __('View & edit workers'),
                'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87',
                'badge' => $stats['active'],
            ])
        </div>

        {{-- ── counts ── --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mt-4">
            <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Active Workers') }}</p>
                <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white tracking-tight mt-1">{{ $stats['active'] }}</p>
            </div>
            <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Inactive Workers') }}</p>
                <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white tracking-tight mt-1">{{ $stats['inactive'] }}</p>
            </div>
        </div>

        <p class="text-xs text-gray-400 dark:text-gray-500 font-sans text-center mt-6">
            {{ __('Every worker gets an Employee ID (EMP-0001…) automatically. Owners do not need one.') }}
        </p>
    </main>
@endsection
