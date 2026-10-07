@extends('layouts.frontend')

@php
    $icons = [
        'tile' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 8l2 2 4-4',
        'plus' => 'M12 4v16m8-8H4',
        'person' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'check' => 'M5 13l4 4L19 7',
        'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'money' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'save' => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'list' => 'M4 6h16M4 12h16M4 18h10',
        'print' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
        'chart' => 'M9 19V6m6 13V10M3 19V12m18 7V3',
        'tag' => 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87',
    ];

    $sections = [
        [
            'id' => 'attendance',
            'title' => __('Check in and check out'),
            'who' => __('For the gate or office — every morning and evening'),
            'link' => ['url' => route('attendance'), 'label' => __('Check In / Out')],
            'steps' => [
                ['tile', __('On the home screen, press "Attendance", then "Check In / Out".')],
                ['check', __('When a person arrives, press the green "Check In" button on their name.')],
                ['person', __('When a labourer leaves, press "Check Out". Ask what they made, sorted, loaded or dispatched and type how many.')],
                ['save', __('Press "Check Out & Save Work". Their work for the day is saved too.')],
            ],
            'tips' => [
                __('With a fingerprint machine, people just scan their finger: the first scan checks in, the next checks out.'),
                __('Admins: "Attendance Board" shows who is working right now. "Who Checks In" lets you tick who uses attendance.'),
            ],
        ],
        [
            'id' => 'daily-work',
            'title' => __('Every evening: enter the day\'s work'),
            'who' => __('For office staff — do this every day before going home'),
            'link' => ['url' => route('labour.work.create'), 'label' => __('Enter Today\'s Work')],
            'steps' => [
                ['tile', __('On the home screen, press "Attendance".')],
                ['plus', __('Press the blue button "Enter Today\'s Work".')],
                ['person', __('Choose the worker\'s name.')],
                ['check', __('Press "Present", "Half day" or "Absent".')],
                ['box', __('Ask the worker what they did. For each job choose the product, the work (Making, Sorting or Loading) and type how many.')],
                ['money', __('The money earned shows on the right side. Check it with the worker.')],
                ['save', __('Press "Save & Next Worker" and do the next person.')],
            ],
            'tips' => [
                __('Forgot someone? Open "Daily Work". People not done yet show "Not entered" — press "Enter".'),
                __('Made a mistake? Open "Daily Work" and press "Edit" next to the name.'),
                __('Press "+ Add work" if the worker did more than one job.'),
            ],
        ],
        [
            'id' => 'salary',
            'title' => __('Paying salary'),
            'who' => __('Pay for one day, one week or one month — it is your choice'),
            'link' => ['url' => route('labour.salary'), 'label' => __('Pay Salary')],
            'steps' => [
                ['money', __('Open "Attendance", then press "Pay Salary".')],
                ['list', __('People who are owed money are at the top. Press "Pay" next to the name.')],
                ['check', __('Check the days and work shown. Change the "From" and "To" dates if needed.')],
                ['tag', __('If the worker took an advance, type it in "Deductions". Type any extra money in "Bonus".')],
                ['save', __('Press "Pay & Issue Payslip". Give the money.')],
                ['print', __('Press "Print Payslip" and get the worker\'s signature.')],
            ],
            'tips' => [
                __('Paid days are locked and cannot be changed. This stops paying twice.'),
                __('Monthly staff: their monthly salary is filled in for you.'),
            ],
        ],
        [
            'id' => 'stock',
            'title' => __('Checking stock in the yard'),
            'who' => __('See how much was made and how much went out'),
            'link' => ['url' => route('labour.production'), 'label' => __('Production Report')],
            'steps' => [
                ['chart', __('Open "Attendance", then press "Production Report".')],
                ['list', __('Choose the dates and press "Show".')],
                ['box', __('"Produced" = made. "Dispatched" = loaded onto lorries. "Difference" = still in the yard.')],
            ],
            'tips' => [
                __('A red number means more went out than was made. Check that all the work was entered.'),
            ],
        ],
    ];

    $adminSteps = [
        ['box', __('Raw Materials'), __('Add each material (explosives, diesel...) and what it costs. When the price changes, change it here — all product costs update by themselves.'), route('products.materials')],
        ['money', __('Work Rates'), __('Set how much a worker gets for each unit: making, sorting, loading.'), route('products.activities')],
        ['tag', __('Products'), __('Add a product: the materials for one unit, the work rates, commission and selling price. Green = profit, red = loss.'), route('products.create')],
        ['users', __('Workers'), __('Add every worker and choose how they are paid: by work done, by the day or by the month.'), route('labour.workers.create')],
    ];

    $words = [
        __('Produced') => __('Units made (Making work).'),
        __('Dispatched') => __('Units loaded onto customer vehicles (Loading work).'),
        __('Unpaid') => __('Money earned by the worker but not paid yet.'),
        __('Commission') => __('Money given to the agent or broker for each unit sold.'),
        __('Break-even Price') => __('Cost + commission. Selling below this is a loss.'),
        __('Per work done') => __('Paid for each unit: e.g. Rs. 3 for making one, Rs. 5 for loading one.'),
    ];
@endphp

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('How to Use the System'),
        'crumbs' => [__('Help') => null],
        'subtitle' => __('Simple steps. Follow the numbers.'),
    ])

    <main class="px-5 pt-5 pb-28 max-w-4xl mx-auto space-y-6">

        {{-- ── language ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-surface border border-subtle p-4 print:hidden">
            <p class="text-sm text-gray-700 dark:text-gray-200">සිංහලෙන් කියවීමට / Read in English</p>
            <div class="flex gap-2">
                <a href="{{ route('lang.switch', 'si') }}" class="px-4 py-2 rounded-xl text-sm font-semibold {{ app()->getLocale() === 'si' ? 'bg-brand text-white' : 'border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200' }}">සිංහල</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-4 py-2 rounded-xl text-sm font-semibold {{ app()->getLocale() === 'en' ? 'bg-brand text-white' : 'border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200' }}">English</a>
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200">{{ __('Print') }}</button>
            </div>
        </div>

        {{-- ── jump to ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 print:hidden">
            @foreach ($sections as $section)
                <a href="#{{ $section['id'] }}" class="flex flex-col items-center justify-center gap-2 h-24 rounded-2xl bg-surface border border-subtle shadow-sm text-center px-2 active:scale-[0.97] transition-all">
                    <span class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-heading font-semibold">{{ $loop->iteration }}</span>
                    <span class="text-[13px] font-medium text-gray-900 dark:text-white leading-tight">{{ $section['title'] }}</span>
                </a>
            @endforeach
            @can('manage-pricing')
                <a href="#admin" class="flex flex-col items-center justify-center gap-2 h-24 rounded-2xl bg-surface border border-subtle shadow-sm text-center px-2 active:scale-[0.97] transition-all">
                    <span class="w-9 h-9 rounded-full bg-amber-500 text-white flex items-center justify-center font-heading font-semibold">★</span>
                    <span class="text-[13px] font-medium text-gray-900 dark:text-white leading-tight">{{ __('Admin: first-time setup') }}</span>
                </a>
            @endcan
        </div>

        {{-- ── example ── --}}
        <div class="rounded-2xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 p-5">
            <p class="font-heading font-semibold text-gray-900 dark:text-white">{{ __('Example') }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">{{ __('In the evening Sunil says: "Today I sorted 5 of product A, loaded 10 of product B and made 10 of product C."') }}</p>
            <div class="mt-3 rounded-xl bg-surface border border-subtle divide-y divide-gray-100 dark:divide-[#1c3350] text-sm">
                @foreach ([['A', __('Sorting & Transport'), 5, 5], ['B', __('Loading'), 10, 5], ['C', __('Making'), 10, 3]] as [$product, $work, $qty, $rate])
                    <div class="flex items-center justify-between px-4 py-2">
                        <span class="text-gray-700 dark:text-gray-200">{{ __('Product') }} {{ $product }} · {{ $work }} · <b>{{ $qty }}</b></span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $qty }} × Rs. {{ $rate }} = <b class="text-gray-900 dark:text-white">Rs. {{ $qty * $rate }}</b></span>
                    </div>
                @endforeach
                <div class="flex items-center justify-between px-4 py-2 font-semibold text-gray-900 dark:text-white">
                    <span>{{ __('Total earned') }}</span><span>Rs. 105</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('You only type the numbers. The system does the maths.') }}</p>
        </div>

        {{-- ── sections ── --}}
        @foreach ($sections as $section)
            <section id="{{ $section['id'] }}" class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 scroll-mt-4 break-inside-avoid">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-full bg-brand text-white flex items-center justify-center font-heading font-semibold text-lg">{{ $loop->iteration }}</span>
                    <div>
                        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ $section['title'] }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $section['who'] }}</p>
                    </div>
                </div>

                <ol class="mt-4 space-y-2">
                    @foreach ($section['steps'] as [$icon, $text])
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-surface-alt">
                            <span class="w-8 h-8 shrink-0 rounded-full border-2 border-[#004080] dark:border-blue-400 text-[#004080] dark:text-blue-300 flex items-center justify-center font-semibold text-sm">{{ $loop->iteration }}</span>
                            <svg class="w-6 h-6 shrink-0 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}"/>
                            </svg>
                            <span class="text-[15px] text-gray-900 dark:text-white leading-snug">{{ $text }}</span>
                        </li>
                    @endforeach
                </ol>

                @if (! empty($section['tips']))
                    <ul class="mt-4 space-y-1.5">
                        @foreach ($section['tips'] as $tip)
                            <li class="flex gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="shrink-0">💡</span><span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <a href="{{ $section['link']['url'] }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2.5 rounded-xl bg-brand text-white text-sm font-semibold active:scale-95 transition-transform print:hidden">
                    {{ $section['link']['label'] }} →
                </a>
            </section>
        @endforeach

        {{-- ── admin setup ── --}}
        @can('manage-pricing')
            <section id="admin" class="rounded-2xl bg-surface border-2 border-amber-300 dark:border-amber-500/40 p-5 scroll-mt-4 break-inside-avoid">
                <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">★ {{ __('Admin: first-time setup') }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Only Admin and Super Admin can see costs and change prices. Do these once, in this order.') }}</p>
                <ol class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($adminSteps as [$icon, $title, $text, $url])
                        <li>
                            <a href="{{ $url }}" class="flex gap-3 h-full p-4 rounded-xl bg-surface-alt hover:shadow-md transition-shadow">
                                <span class="w-8 h-8 shrink-0 rounded-full bg-amber-500 text-white flex items-center justify-center font-semibold text-sm">{{ $loop->iteration }}</span>
                                <span>
                                    <span class="block font-medium text-gray-900 dark:text-white">{{ $title }}</span>
                                    <span class="block text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ $text }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endcan

        {{-- ── words ── --}}
        <section class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 break-inside-avoid">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('What the words mean') }}</h2>
            <dl class="mt-3 divide-y divide-gray-100 dark:divide-[#1c3350]">
                @foreach ($words as $word => $meaning)
                    <div class="py-2.5 sm:flex sm:gap-4">
                        <dt class="font-medium text-gray-900 dark:text-white sm:w-44 shrink-0">{{ $word }}</dt>
                        <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $meaning }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </main>
@endsection
