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
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'receipt' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
        'branch' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
    ];

    $rs = fn ($amount) => 'Rs. '.number_format($amount, 2);

    // ── The worked example used all through the page. Every figure is calculated here
    //    with the same rules the system uses, so the help never shows wrong maths.
    $dayWork = [['A', __('Sorting & Transport'), 50, 5], ['B', __('Loading'), 60, 5], ['C', __('Making'), 50, 3]];
    $dayTotal = collect($dayWork)->sum(fn ($line) => $line[2] * $line[3]);   // 700
    $workDays = 6;
    $weekEarned = $dayTotal * $workDays;                                     // 4,200
    $bonus = 200;
    $advance = 1000;
    $epfEmployee = round($weekEarned * 0.08, 2);                             // bonus is not counted for EPF
    $epfEmployer = round($weekEarned * 0.12, 2);
    $etf = round($weekEarned * 0.03, 2);
    $net = $weekEarned + $bonus - $advance - $epfEmployee;                   // 3,064

    $monthly = 45000;
    $monthlyNet = $monthly - $monthly * 0.08;

    // ── How the pieces connect.
    $flow = [
        ['tag', __('Set up'), __('Admin adds materials, work rates, products and workers. Only once.')],
        ['check', __('Check in'), __('Morning: each person checks in at the gate.')],
        ['box', __('Record work'), __('Evening: type what each labourer made, sorted or loaded.')],
        ['money', __('Pay'), __('Pay for a day, a week or a month. Print the payslip.')],
        ['chart', __('Check'), __('See stock in the yard, EPF / ETF to send, and expenses.')],
    ];

    $cast = [
        ['N', 'Nimal', __('Admin — sets prices and rates'), 'bg-amber-500'],
        ['K', 'Kamala', __('Office staff — enters work and pays salary'), 'bg-[#004080]'],
        ['S', 'Sunil', __('Labourer — paid per work done, registered for EPF'), 'bg-green-600'],
    ];

    // ── One full week, step 1 to the last step. [when, who, title, actions, result, link]
    $story = [
        [__('Day 1'), 'Nimal', __('Set the costs and work rates'), [
            __('Home → "Products" → "Raw Materials". Add each material (diesel, explosives…) and its price.'),
            __('Open "Work Rates". Set: Making Rs. 3, Sorting & Transport Rs. 5, Loading Rs. 5 for one unit.'),
        ], __('Every product cost is now worked out from these prices. Change a price later and all costs update by themselves.'), auth()->user()?->can('manage-pricing') ? route('products.activities') : null],

        [__('Day 1'), 'Nimal', __('Add the products'), [
            __('"Products" → add Product A, B and C.'),
            __('For each one, type the materials for one unit, the work rates, commission and selling price.'),
        ], __('Green price = profit, red price = loss. Now the products can be picked when work is entered.'), auth()->user()?->can('manage-pricing') ? route('products.create') : null],

        [__('Day 1'), 'Kamala', __('Register Sunil as a worker'), [
            __('Home → "Workers" → "Register New Worker".'),
            __('Type his name, choose his branch and choose pay type "Per work done".'),
            __('Tick "Calculate EPF / ETF" and type his EPF No. Leave the rates at 8% / 12% / 3%.'),
            __('Press Save.'),
        ], __('Sunil gets an Employee ID (EMP-0001…) automatically and is ready to check in.'), route('labour.workers.create')],

        [__('Monday · 7:30 AM'), 'Kamala', __('Sunil arrives'), [
            __('Home → "Attendance" → "Check In / Check Out".'),
            __('Press the green "Check In" button on Sunil\'s name. (With a fingerprint machine, Sunil just scans his finger.)'),
        ], __('Sunil moves to "Working Now". His arrival time is saved.'), route('attendance')],

        [__('Monday · 5:00 PM'), 'Kamala', __('Sunil goes home'), [
            __('Press "Check Out" on Sunil\'s name.'),
            __('Ask him: "What did you do today?" He says: sorted 50 of Product A, loaded 60 of Product B, made 50 of Product C.'),
            __('Type each product, the work and the number. Press "Check Out & Save Work".'),
        ], __('Monday is saved: Present, :amount earned. See the table below.', ['amount' => $rs($dayTotal)]), null],

        [__('Monday · 6:00 PM'), 'Kamala', __('Check that everybody is entered'), [
            __('Home → "Salary & Work" → "Daily Work Sheet".'),
            __('Anybody showing "Not entered" (for example, someone who did not check out) → press "Enter".'),
            __('Choose Present, Half day or Absent, add their work and press "Save & Next Worker".'),
        ], __('The sheet shows how much of each product was made and dispatched today, and the wages earned.'), route('labour.work')],

        [__('Wednesday'), 'Kamala', __('Sunil works at the other branch for a day'), [
            __('Open "Check In / Check Out" for the branch where he is working today.'),
            __('In the box "Working here today from another branch?" choose Sunil and press "Check In Here".'),
        ], __('That day\'s work counts for the branch he worked at. His home branch shows where he was.'), null],

        [__('Thursday'), 'Kamala', __('Sunil asks for an advance'), [
            __('Give him Rs. 1,000 and write it down.'),
            __('Do not change his work. You will take the advance off on payday.'),
        ], __('Nothing changes in the system yet.'), null],

        [__('Saturday · payday'), 'Kamala', __('Pay Sunil for the week'), [
            __('Home → "Salary & Work" → "Pay Salary". People who are owed money are at the top.'),
            __('Press "Pay" next to Sunil. Set "From" Monday and "To" Saturday.'),
            __('Type 200 in "Bonus / allowance" and 1000 in "Deductions / advances".'),
            __('EPF is worked out by itself. Check the payslip below with Sunil, then press "Pay & Issue Payslip".'),
        ], __('Give Sunil :amount in cash.', ['amount' => $rs($net)]), route('labour.salary')],

        [__('Saturday · payday'), 'Kamala', __('Print and sign'), [
            __('Press "Print Payslip". Sunil signs it. Keep it in the file.'),
        ], __('Monday to Saturday is now locked. Nobody can change it or pay it twice.'), null],

        [__('End of month'), 'Nimal', __('Check stock and send EPF / ETF'), [
            __('"Salary & Work" → "Production Report". Choose the month, press "Show". Check what is left in the yard.'),
            __('"Pay Salary" shows "EPF This Month" and "ETF This Month". Send these amounts to the EPF and ETF funds.'),
        ], __('For Sunil\'s week: EPF :epf (his 8% + business 12%) and ETF :etf.', ['epf' => $rs($epfEmployee + $epfEmployer), 'etf' => $rs($etf)]), route('labour.production')],
    ];

    $sections = [
        [
            'id' => 'attendance',
            'title' => __('Check in and check out'),
            'who' => __('For the gate or office — every morning and evening'),
            'link' => ['url' => route('attendance'), 'label' => __('Check In / Check Out')],
            'steps' => [
                ['tile', __('Home → "Attendance" → "Check In / Check Out".')],
                ['check', __('When a person arrives, press the green "Check In" button on their name.')],
                ['branch', __('Someone from another branch working here today? Choose their name in the box at the top and press "Check In Here".')],
                ['person', __('When a labourer leaves, press "Check Out". Ask what they made, sorted, loaded or dispatched and type how many.')],
                ['save', __('Press "Check Out & Save Work". Their work for the day is saved too.')],
            ],
            'tips' => [
                __('With a fingerprint machine, people just scan their finger: the first scan checks in, the next checks out.'),
                __('Scanned twice by mistake? A second scan within 2 minutes is ignored.'),
                __('A shift shorter than 4 hours is marked "Half day" by itself.'),
                __('Admins: "Live Board" shows who is working right now. "Who Checks In" lets you tick who uses attendance.'),
            ],
        ],
        [
            'id' => 'daily-work',
            'title' => __('Every evening: enter the day\'s work'),
            'who' => __('For office staff — do this every day before going home'),
            'link' => ['url' => route('labour.work'), 'label' => __('Daily Work Sheet')],
            'steps' => [
                ['tile', __('Home → "Salary & Work" → "Daily Work Sheet".')],
                ['plus', __('Press "Enter Work", or "Enter" next to a name that shows "Not entered".')],
                ['person', __('Choose the worker\'s name.')],
                ['check', __('Press "Present", "Half day" or "Absent".')],
                ['box', __('Ask the worker what they did. For each job choose the product, the work (Making, Sorting or Loading) and type how many.')],
                ['money', __('The money earned shows on the right side. Check it with the worker.')],
                ['save', __('Press "Save & Next Worker" and do the next person.')],
            ],
            'tips' => [
                __('Work saved at check-out is already here. You only need to enter people who were missed.'),
                __('Made a mistake? Open "Daily Work" and press "Edit" next to the name.'),
                __('Press "Add work" if the worker did more than one job.'),
                __('Daily-wage and monthly workers: still type what they made. It counts for the stock, not for their pay.'),
            ],
        ],
        [
            'id' => 'salary',
            'title' => __('Paying salary'),
            'who' => __('Pay for one day, one week or one month — it is your choice'),
            'link' => ['url' => route('labour.salary'), 'label' => __('Pay Salary')],
            'steps' => [
                ['money', __('Home → "Salary & Work" → "Pay Salary".')],
                ['list', __('People who are owed money are at the top. Press "Pay" next to the name.')],
                ['check', __('Check the days and work shown. Change the "From" and "To" dates if needed.')],
                ['tag', __('If the worker took an advance, type it in "Deductions / advances". Type any extra money in "Bonus / allowance".')],
                ['shield', __('If the worker is registered for EPF, the 8% is taken off by itself. Check the "Net pay".')],
                ['save', __('Press "Pay & Issue Payslip". Give the money.')],
                ['print', __('Press "Print Payslip" and get the worker\'s signature.')],
            ],
            'tips' => [
                __('Paid days are locked and cannot be changed. This stops paying twice.'),
                __('Monthly staff: their monthly salary is filled in for you.'),
                __('Daily-wage workers get their day rate for Present and half of it for Half day.'),
            ],
        ],
        [
            'id' => 'epf',
            'title' => __('EPF and ETF'),
            'who' => __('Only for workers registered for EPF / ETF'),
            'link' => ['url' => route('labour.workers'), 'label' => __('Workers')],
            'steps' => [
                ['person', __('Home → "Workers" → "All Workers". Press "Edit" on the worker.')],
                ['check', __('Tick "Calculate EPF / ETF" and type the EPF No. Leave the rates at 8% / 12% / 3% unless told otherwise. Save.')],
                ['money', __('On payday the worker\'s 8% is taken off their pay. The business pays 12% EPF and 3% ETF on top.')],
                ['chart', __('At month end, open "Pay Salary" and look at "EPF This Month" and "ETF This Month". Send those amounts to the funds.')],
            ],
            'tips' => [
                __('EPF / ETF is worked out on work earnings and basic salary only — not on the bonus.'),
                __('Old payslips keep the rates used on that day, even if you change the rates later.'),
                __('Leave the box unticked for workers not registered. Their payslip says "No EPF / ETF for this worker."'),
            ],
        ],
        [
            'id' => 'stock',
            'title' => __('Checking stock in the yard'),
            'who' => __('See how much was made and how much went out'),
            'link' => ['url' => route('labour.production'), 'label' => __('Production Report')],
            'steps' => [
                ['chart', __('Home → "Salary & Work" → "Production Report".')],
                ['list', __('Choose the dates and press "Show".')],
                ['box', __('"Produced" = made. "Dispatched" = loaded onto lorries. "Difference" = still in the yard.')],
            ],
            'tips' => [
                __('A red number means more went out than was made. Check that all the work was entered.'),
            ],
        ],
        [
            'id' => 'expenses',
            'title' => __('Writing down expenses'),
            'who' => __('Every bill the business pays: diesel, repairs, electricity…'),
            'link' => ['url' => route('expenses.create'), 'label' => __('Add Expense')],
            'steps' => [
                ['tile', __('Home → "Expenses" → "Add Expense".')],
                ['tag', __('Choose the expense type, type the amount and who was paid.')],
                ['money', __('Choose how it was paid (cash, bank…) and the date.')],
                ['receipt', __('Take a photo of the bill and attach it.')],
                ['save', __('Press "Save Expense", or "Save & Add Another" for the next bill.')],
            ],
            'tips' => [
                __('Missing a type? Open "Manage types" and add one.'),
                __('The Expenses page shows spending by type for this week or this month.'),
            ],
        ],
    ];

    // ── Common situations. [question, answer, icon]
    $whatIf = [
        [__('Someone forgot to check out yesterday'), __('An admin opens "Attendance" → "Attendance History", presses "Edit" on that day and types the leaving time. Then enter their work on the "Daily Work Sheet" for that date.'), 'person'],
        [__('A person is missing from the Check In list'), __('An admin opens "Attendance" → "Who Checks In" and ticks their name. Also check the worker is Active.'), 'users'],
        [__('A worker left after 3 hours'), __('Check them out as normal. A shift under 4 hours is marked "Half day". Daily-wage workers get half their day rate.'), 'check'],
        [__('I typed the wrong number'), __('"Daily Work Sheet" → choose the date → "Edit" next to the name. If that day is already paid, it is locked and cannot be changed.'), 'list'],
        [__('A worker took an advance'), __('Give the money and write it down. On payday type it in "Deductions / advances". It comes off the net pay.'), 'tag'],
        [__('Someone worked at another branch today'), __('At that branch\'s Check In screen, choose them under "Working here today from another branch?" and press "Check In Here".'), 'branch'],
        [__('A red number in the Production Report'), __('More went out than was made in those dates. Some work was not entered, or the stock was made before the start date.'), 'chart'],
        [__('Paying monthly office staff'), __('Press "Pay". The monthly salary is filled in. Example: :salary with EPF → :epf comes off, they get :net.', ['salary' => $rs($monthly), 'epf' => $rs($monthly * 0.08), 'net' => $rs($monthlyNet)]), 'money'],
    ];

    $adminSteps = [
        ['box', __('Raw Materials'), __('Add each material (explosives, diesel...) and what it costs. When the price changes, change it here — all product costs update by themselves.'), route('products.materials')],
        ['money', __('Work Rates'), __('Set how much a worker gets for each unit: making, sorting, loading.'), route('products.activities')],
        ['tag', __('Products'), __('Add a product: the materials for one unit, the work rates, commission and selling price. Green = profit, red = loss.'), route('products.create')],
        ['users', __('Workers'), __('Add every worker, choose how they are paid (by work done, by the day or by the month) and tick EPF / ETF if they are registered.'), route('labour.workers.create')],
    ];

    $words = [
        __('Produced') => __('Units made (Making work).'),
        __('Dispatched') => __('Units loaded onto customer vehicles (Loading work).'),
        __('Unpaid') => __('Money earned by the worker but not paid yet.'),
        __('Half day') => __('Worked less than 4 hours. Daily-wage workers get half the day rate.'),
        __('Advance') => __('Money given before payday. Taken off on payday as a deduction.'),
        __('Net pay') => __('What the worker takes home: earnings + bonus − deductions − their EPF.'),
        'EPF' => __('Employees\' Provident Fund. Worker pays 8% (taken from pay), business pays 12%.'),
        'ETF' => __('Employees\' Trust Fund. Business pays 3%. Nothing is taken from the worker.'),
        __('Commission') => __('Money given to the agent or broker for each unit sold.'),
        __('Break-even Price') => __('Cost + commission. Selling below this is a loss.'),
        __('Per work done') => __('Paid for each unit: e.g. Rs. 3 for making one, Rs. 5 for loading one.'),
    ];

    $jump = collect($sections)->map(fn ($s) => [$s['id'], $s['title']])
        ->prepend(['story', __('Full example: one week')])
        ->push(['what-if', __('What if…?')]);
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

        {{-- ── how it works ── --}}
        <section class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 break-inside-avoid">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('How it works') }}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Five steps. Each one feeds the next.') }}</p>
            <ol class="mt-4 grid grid-cols-1 sm:grid-cols-5 gap-2">
                @foreach ($flow as [$icon, $title, $text])
                    <li class="relative flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 p-3 rounded-xl bg-surface-alt">
                        <span class="relative w-11 h-11 shrink-0 rounded-full bg-brand text-white flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}"/></svg>
                            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-500 text-[11px] font-semibold flex items-center justify-center">{{ $loop->iteration }}</span>
                        </span>
                        <span>
                            <span class="block font-medium text-gray-900 dark:text-white">{{ $title }}</span>
                            <span class="block text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-snug">{{ $text }}</span>
                        </span>
                        @unless ($loop->last)
                            <span class="hidden sm:block absolute -right-2 top-8 z-10 text-gray-400 dark:text-gray-500 text-sm">▶</span>
                        @endunless
                    </li>
                @endforeach
            </ol>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-4">{{ __('Every number you type flows on: work → wages → payslip → reports. You never add up by hand.') }}</p>
        </section>

        {{-- ── jump to ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 print:hidden">
            @foreach ($jump as [$id, $title])
                <a href="#{{ $id }}" class="flex flex-col items-center justify-center gap-2 h-24 rounded-2xl bg-surface border border-subtle shadow-sm text-center px-2 active:scale-[0.97] transition-all">
                    <span class="w-9 h-9 rounded-full {{ in_array($id, ['story', 'what-if']) ? 'bg-green-600' : 'bg-brand' }} text-white flex items-center justify-center font-heading font-semibold">
                        {{ match ($id) { 'story' => '▶', 'what-if' => '?', default => $loop->iteration - 1 } }}
                    </span>
                    <span class="text-[13px] font-medium text-gray-900 dark:text-white leading-tight">{{ $title }}</span>
                </a>
            @endforeach
            @can('manage-pricing')
                <a href="#admin" class="flex flex-col items-center justify-center gap-2 h-24 rounded-2xl bg-surface border border-subtle shadow-sm text-center px-2 active:scale-[0.97] transition-all">
                    <span class="w-9 h-9 rounded-full bg-amber-500 text-white flex items-center justify-center font-heading font-semibold">★</span>
                    <span class="text-[13px] font-medium text-gray-900 dark:text-white leading-tight">{{ __('Admin: first-time setup') }}</span>
                </a>
            @endcan
        </div>

        {{-- ── full example: one week, first step to last ── --}}
        <section id="story" class="rounded-2xl bg-surface border-2 border-green-300 dark:border-green-500/40 p-5 scroll-mt-4">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">▶ {{ __('Full example: one week with the system') }}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Follow Sunil from his first day to his payslip. Do the same for your own workers.') }}</p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                @foreach ($cast as [$initial, $name, $role, $color])
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-alt">
                        <span class="w-9 h-9 shrink-0 rounded-full {{ $color }} text-white flex items-center justify-center font-semibold">{{ $initial }}</span>
                        <span>
                            <span class="block font-medium text-gray-900 dark:text-white">{{ $name }}</span>
                            <span class="block text-xs text-gray-600 dark:text-gray-300 leading-snug">{{ $role }}</span>
                        </span>
                    </div>
                @endforeach
            </div>

            @php $castColor = collect($cast)->mapWithKeys(fn ($c) => [$c[1] => $c[3]]); @endphp

            <ol class="mt-5 relative border-l-2 border-dashed border-gray-300 dark:border-[#2a4a70] ml-4 space-y-5">
                @foreach ($story as [$when, $who, $title, $actions, $result, $url])
                    <li class="relative pl-7 break-inside-avoid">
                        <span class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-semibold text-sm ring-4 ring-white dark:ring-[#0b1a2e]">{{ $loop->iteration }}</span>
                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-wide">
                            <span class="text-gray-500 dark:text-gray-400">{{ $when }}</span>
                            <span class="px-2 py-0.5 rounded-full text-white {{ $castColor[$who] }}">{{ $who }}</span>
                        </div>
                        <h3 class="font-heading font-semibold text-gray-900 dark:text-white mt-1">{{ $title }}</h3>
                        <ul class="mt-2 space-y-1.5">
                            @foreach ($actions as $action)
                                <li class="flex gap-2 text-[15px] text-gray-800 dark:text-gray-100 leading-snug">
                                    <span class="text-gray-400 shrink-0">•</span><span>{{ $action }}</span>
                                </li>
                            @endforeach
                        </ul>

                        {{-- the day's work, after check-out --}}
                        @if ($loop->iteration === 5)
                            <div class="mt-3 rounded-xl bg-surface-alt border border-subtle divide-y divide-gray-100 dark:divide-[#1c3350] text-sm">
                                @foreach ($dayWork as [$product, $work, $qty, $rate])
                                    <div class="flex items-center justify-between gap-3 px-4 py-2">
                                        <span class="text-gray-700 dark:text-gray-200">{{ __('Product') }} {{ $product }} · {{ $work }} · <b>{{ $qty }}</b></span>
                                        <span class="text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $qty }} × Rs. {{ $rate }} = <b class="text-gray-900 dark:text-white">Rs. {{ number_format($qty * $rate) }}</b></span>
                                    </div>
                                @endforeach
                                <div class="flex items-center justify-between px-4 py-2 font-semibold text-gray-900 dark:text-white">
                                    <span>{{ __('Total earned') }}</span><span>{{ $rs($dayTotal) }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('You only type the numbers. The system does the maths.') }}</p>
                        @endif

                        {{-- the payslip --}}
                        @if ($loop->iteration === 9)
                            <div class="mt-3 rounded-xl bg-surface-alt border border-subtle divide-y divide-gray-100 dark:divide-[#1c3350] text-sm">
                                @foreach ([
                                    [__('Work done (:days days × :amount)', ['days' => $workDays, 'amount' => $rs($dayTotal)]), '', $weekEarned],
                                    [__('Bonus / allowance'), '+', $bonus],
                                    [__('Deductions / advances'), '−', $advance],
                                    [__('EPF – employee (:rate%)', ['rate' => 8]), '−', $epfEmployee],
                                ] as [$label, $sign, $amount])
                                    <div class="flex items-center justify-between gap-3 px-4 py-2 text-gray-700 dark:text-gray-200">
                                        <span>{{ $label }}</span><span class="whitespace-nowrap">{{ $sign }} {{ $rs($amount) }}</span>
                                    </div>
                                @endforeach
                                <div class="flex items-center justify-between px-4 py-2 font-semibold text-gray-900 dark:text-white">
                                    <span>{{ __('Net pay') }}</span><span>{{ $rs($net) }}</span>
                                </div>
                                <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Paid by business') }}: {{ __('EPF :rate%', ['rate' => 12]) }} {{ $rs($epfEmployer) }} · {{ __('ETF :rate%', ['rate' => 3]) }} {{ $rs($etf) }}.
                                    {{ __('EPF / ETF is worked out on work earnings and basic salary only — not on the bonus.') }}
                                </div>
                            </div>
                        @endif

                        <p class="mt-3 flex gap-2 p-3 rounded-xl bg-green-50 dark:bg-green-500/10 text-sm text-green-900 dark:text-green-200">
                            <span class="shrink-0">✓</span><span>{{ $result }}</span>
                        </p>
                        @if ($url)
                            <a href="{{ $url }}" class="inline-block mt-2 text-sm font-semibold text-brand dark:text-blue-300 underline print:hidden">{{ __('Open this page') }} →</a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </section>

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

        {{-- ── what if…? ── --}}
        <section id="what-if" class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 scroll-mt-4">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('What if…?') }}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Common situations and what to do.') }}</p>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($whatIf as [$question, $answer, $icon])
                    <div class="flex gap-3 p-4 rounded-xl bg-surface-alt break-inside-avoid">
                        <svg class="w-6 h-6 shrink-0 text-[#004080] dark:text-blue-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}"/>
                        </svg>
                        <span>
                            <span class="block font-medium text-gray-900 dark:text-white">{{ $question }}</span>
                            <span class="block text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $answer }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </section>

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
