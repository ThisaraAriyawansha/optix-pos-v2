@extends('layouts.frontend')

@use('App\Models\Attendance')
@use('App\Support\AttendanceClock')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Attendance Board'),
        'crumbs' => [__('Attendance') => route('attendance.menu'), __('Live Board') => null],
        'subtitle' => $date->translatedFormat('l, d F Y'),
        'actions' => [
            ['url' => route('attendance.people'), 'label' => __('Who Checks In'), 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87'],
            ['url' => route('attendance.report'), 'label' => __('History'), 'icon' => 'M4 6h16M4 12h16M4 18h10'],
            ['url' => route('attendance'), 'label' => __('Check In / Out'), 'icon' => 'M5 13l4 4L19 7', 'primary' => true],
        ],
    ])

    @php
        $filterField = 'px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
        // Validated two-series palette (labourers / staff), separate steps for light and dark.
        $workerBar = 'bg-[#2f6fb8] dark:bg-[#4a8ad4]';
        $staffBar = 'bg-[#c77d12] dark:bg-[#c07f1c]';
        $span = max(1, $windowStart->diffInMinutes($windowEnd));
        $pct = fn ($time) => round(max(0, min(100, $windowStart->diffInMinutes($time) / $span * 100)), 2);
        $peak = max(1, $hours->max(fn ($hour) => $hour['workers'] + $hour['staff']));
        $working = $rows->where('status', 'in');
        $withShifts = $rows->filter(fn ($row) => $row['shifts']->isNotEmpty());
        $absent = $rows->where('status', 'absent');
    @endphp

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('attendance.board') }}" method="GET" class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('attendance.board', ['date' => $date->copy()->subDay()->toDateString(), 'branch_id' => $branchId]) }}" class="{{ $filterField }}" title="{{ __('Previous day') }}">←</a>
            <input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}" onchange="this.form.submit()" class="{{ $filterField }}">
            @unless ($isToday)
                <a href="{{ route('attendance.board', ['date' => $date->copy()->addDay()->toDateString(), 'branch_id' => $branchId]) }}" class="{{ $filterField }}" title="{{ __('Next day') }}">→</a>
                <a href="{{ route('attendance.board', ['branch_id' => $branchId]) }}" class="{{ $filterField }}">{{ __('Today') }}</a>
            @endunless
            <select name="branch_id" onchange="this.form.submit()" class="{{ $filterField }}">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
            @if ($isToday)
                <span class="ml-auto flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="relative flex w-2.5 h-2.5">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-60 animate-ping"></span>
                        <span class="relative inline-flex w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    </span>
                    {{ __('Live · updates every minute') }} · <span id="clock">{{ now()->format('h:i A') }}</span>
                </span>
            @endif
        </form>

        {{-- ── headline numbers ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <div class="col-span-2 lg:col-span-1 rounded-2xl bg-brand text-white p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-white/70 font-sans">{{ $isToday ? __('Workers Working Now') : __('Workers Came') }}</p>
                <p class="font-heading font-semibold text-3xl tracking-tight mt-1">
                    {{ $isToday ? $summary['labourers_in'] : $withShifts->where('type', 'worker')->count() }}<span class="text-lg text-white/60"> / {{ $summary['labourers'] }}</span>
                </p>
            </div>
            @foreach ([
                ['label' => $isToday ? __('Staff On Duty') : __('Staff Came'), 'value' => ($isToday ? $summary['staff_in'] : $withShifts->where('type', 'user')->count()).' / '.$summary['staff']],
                ['label' => __('Checked Out'), 'value' => $summary['out']],
                ['label' => __('Not Arrived'), 'value' => $summary['absent']],
                ['label' => __('Total Hours'), 'value' => Attendance::formatMinutes($summary['minutes'])],
            ] as $stat)
                <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm">
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ $stat['label'] }}</p>
                    <p class="font-heading font-semibold text-lg sm:text-xl text-gray-900 dark:text-white tracking-tight mt-1">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        @if ($missed->isNotEmpty())
            <div class="mt-4 rounded-2xl border border-amber-300 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-500/10 p-4">
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">{{ __(':count forgot to check out on an earlier day', ['count' => $missed->count()]) }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach ($missed as $shift)
                        <a href="{{ route('attendance.edit', $shift) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-surface border border-amber-300 dark:border-amber-500/40 text-gray-800 dark:text-gray-200">
                            {{ $shift->attendable->name }} · {{ $shift->work_date->format('d M') }} · {{ __('Fix') }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
            {{-- ── head count by hour ── --}}
            <section class="lg:col-span-2 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('People on Site by Hour') }}</h2>
                    <div class="flex items-center gap-3 text-xs text-gray-600 dark:text-gray-300">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm {{ $workerBar }}"></span>{{ __('Workers') }}</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm {{ $staffBar }}"></span>{{ __('Staff') }}</span>
                    </div>
                </div>

                <div class="relative mt-4">
                    <div class="flex items-end gap-1 h-40 border-b border-gray-200 dark:border-[#24446a]" id="hour-chart">
                        @foreach ($hours as $hour)
                            @php $total = $hour['workers'] + $hour['staff']; @endphp
                            <div class="hour-bar group relative flex-1 h-full flex flex-col justify-end items-stretch cursor-default"
                                 data-tip="{{ $hour['label'] }} — {{ __('Workers') }}: {{ $hour['workers'] }} · {{ __('Staff') }}: {{ $hour['staff'] }}">
                                <div class="absolute inset-0 rounded-md group-hover:bg-gray-100 dark:group-hover:bg-white/5"></div>
                                @if ($total > 0)
                                    @if ($total === $peak)
                                        <span class="relative text-[10px] text-center text-gray-500 dark:text-gray-400 mb-0.5">{{ $total }}</span>
                                    @endif
                                    @if ($hour['staff'] > 0)
                                        <div class="relative {{ $staffBar }} rounded-t-[4px] {{ $hour['workers'] > 0 ? 'mb-[2px]' : '' }}" style="height: {{ $hour['staff'] / $peak * 85 }}%"></div>
                                    @endif
                                    @if ($hour['workers'] > 0)
                                        <div class="relative {{ $workerBar }} {{ $hour['staff'] > 0 ? '' : 'rounded-t-[4px]' }}" style="height: {{ $hour['workers'] / $peak * 85 }}%"></div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-1 mt-1">
                        @foreach ($hours as $hour)
                            <span class="flex-1 text-center text-[10px] text-gray-400 dark:text-gray-500 {{ $loop->index % 2 ? 'hidden sm:block' : '' }}">{{ $hour['label'] }}</span>
                        @endforeach
                    </div>
                    <div id="chart-tip" class="hidden absolute -top-2 left-0 px-2.5 py-1.5 rounded-lg bg-gray-900 text-white text-xs whitespace-nowrap pointer-events-none shadow-lg"></div>
                </div>
            </section>

            {{-- ── recent activity ── --}}
            <section class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5">
                <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Latest Check-ins') }}</h2>
                <ul class="mt-3 space-y-2.5 max-h-56 overflow-y-auto pr-1">
                    @forelse ($events as $event)
                        <li class="flex items-center gap-2.5 text-sm">
                            <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-[11px] font-bold
                                         {{ $event['kind'] === 'in' ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                {{ $event['kind'] === 'in' ? __('IN') : __('OUT') }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-gray-900 dark:text-white">{{ $event['shift']->attendable->name }}</span>
                                <span class="block text-[11px] text-gray-400 dark:text-gray-500">{{ $event['shift']->methodLabel($event['method']) }}</span>
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $event['at']->format('h:i A') }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 py-6 text-center">{{ __('No check-ins yet') }}</li>
                    @endforelse
                </ul>
            </section>
        </div>

        {{-- ── working right now ── --}}
        @if ($isToday)
            <div class="flex items-center justify-between mt-8">
                <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('Working Right Now') }} <span class="text-gray-400 font-normal">({{ $working->count() }})</span></h2>
            </div>
            <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>
            @if ($working->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8 rounded-2xl bg-surface border border-subtle">{{ __('Nobody is checked in right now.') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach ($working as $row)
                        <div class="rounded-2xl bg-surface border border-subtle shadow-sm p-3.5 flex items-center gap-3">
                            <span class="w-1.5 self-stretch rounded-full {{ $row['type'] === 'worker' ? $workerBar : $staffBar }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $row['person']->name }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate">{{ AttendanceClock::roleOf($row['person']) }} · {{ __('since') }} {{ $row['open']->check_in_at->format('h:i A') }}</p>
                            </div>
                            <span class="elapsed font-heading font-semibold text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                  data-minutes="{{ $row['minutes'] }}">{{ Attendance::formatMinutes($row['minutes']) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- ── timeline ── --}}
        <div class="flex items-center justify-between mt-8">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('Day Timeline') }}</h2>
            <a href="{{ route('attendance.report', ['from' => $date->toDateString(), 'to' => $date->toDateString(), 'branch_id' => $branchId]) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand font-sans">{{ __('Details') }} →</a>
        </div>
        <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

        <section class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-4 sm:p-5 overflow-x-auto">
            @if ($withShifts->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">{{ __('No check-ins on this day.') }}</p>
            @else
                <div class="min-w-[36rem]">
                    {{-- hour scale --}}
                    <div class="flex items-center">
                        <div class="w-36 shrink-0"></div>
                        <div class="relative flex-1 h-5">
                            @for ($tick = $windowStart->copy(); $tick->lte($windowEnd); $tick->addHours(2))
                                <span class="absolute -translate-x-1/2 text-[10px] text-gray-400 dark:text-gray-500" style="left: {{ $pct($tick) }}%">{{ $tick->format('ga') }}</span>
                            @endfor
                        </div>
                        <div class="w-16 shrink-0"></div>
                    </div>

                    @foreach ($withShifts as $row)
                        <div class="flex items-center py-1.5 border-t border-gray-100 dark:border-[#1c3350]">
                            <div class="w-36 shrink-0 pr-2 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white truncate">{{ $row['person']->name }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ AttendanceClock::roleOf($row['person']) }}</p>
                            </div>
                            <div class="relative flex-1 h-6 rounded-md bg-gray-50 dark:bg-white/[0.03]">
                                @for ($tick = $windowStart->copy(); $tick->lte($windowEnd); $tick->addHours(2))
                                    <span class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-[#1c3350]" style="left: {{ $pct($tick) }}%"></span>
                                @endfor
                                @foreach ($row['shifts'] as $shift)
                                    @php
                                        $shiftEnd = $shift->check_out_at ?? ($isToday ? now() : $shift->check_in_at->copy()->addMinutes(15));
                                        $left = $pct($shift->check_in_at);
                                        $width = max(1, $pct($shiftEnd) - $left);
                                    @endphp
                                    <div class="absolute top-1 bottom-1 rounded-[4px] {{ $row['type'] === 'worker' ? $workerBar : $staffBar }} {{ $shift->isOpen() ? 'opacity-70 animate-pulse' : '' }}"
                                         style="left: {{ $left }}%; width: {{ $width }}%"
                                         title="{{ $shift->check_in_at->format('h:i A') }} → {{ $shift->check_out_at?->format('h:i A') ?? ($shift->missedCheckOut() ? __('no check-out') : __('now')) }} ({{ Attendance::formatMinutes($shift->minutes()) }})"></div>
                                @endforeach
                            </div>
                            <div class="w-16 shrink-0 text-right text-xs font-medium text-gray-700 dark:text-gray-300">
                                {{ $row['status'] === 'missed' ? __('No out') : Attendance::formatMinutes($row['minutes']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">{{ __('Faded bars are people still checked in. Hover a bar to see the times.') }}</p>
            @endif
        </section>

        {{-- ── not arrived ── --}}
        @if ($absent->isNotEmpty())
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight mt-8">{{ __('Not Arrived') }} <span class="text-gray-400 font-normal">({{ $absent->count() }})</span></h2>
            <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>
            <div class="flex flex-wrap gap-2">
                @foreach ($absent as $row)
                    <span class="px-3 py-1.5 rounded-full text-xs bg-surface border border-subtle text-gray-700 dark:text-gray-300">
                        {{ $row['person']->name }} <span class="text-gray-400">· {{ AttendanceClock::roleOf($row['person']) }}</span>
                    </span>
                @endforeach
            </div>
        @endif
    </main>

    <script>
        // Hover tooltip for the hourly chart.
        (() => {
            const tip = document.getElementById('chart-tip');
            const chart = document.getElementById('hour-chart');
            if (!tip || !chart) return;
            chart.querySelectorAll('.hour-bar').forEach((bar) => {
                bar.addEventListener('mouseenter', () => {
                    tip.textContent = bar.dataset.tip;
                    tip.classList.remove('hidden');
                    const left = bar.offsetLeft + bar.offsetWidth / 2 - tip.offsetWidth / 2;
                    tip.style.left = Math.max(0, Math.min(left, chart.offsetWidth - tip.offsetWidth)) + 'px';
                });
                bar.addEventListener('mouseleave', () => tip.classList.add('hidden'));
            });
        })();

        @if ($isToday)
            // Tick the elapsed time every minute and reload for fresh data.
            (() => {
                const started = Date.now();
                const fmt = (m) => Math.floor(m / 60) + 'h ' + String(m % 60).padStart(2, '0') + 'm';
                setInterval(() => {
                    const passed = Math.floor((Date.now() - started) / 60000);
                    document.querySelectorAll('.elapsed').forEach((el) => el.textContent = fmt(parseInt(el.dataset.minutes) + passed));
                    document.getElementById('clock').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }, 15000);
                setTimeout(() => location.reload(), 60000);
            })();
        @endif
    </script>
@endsection
