@extends('layouts.frontend')

@use('App\Models\Attendance')
@use('App\Support\AttendanceClock')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Attendance History'),
        'crumbs' => [__('Attendance') => route('attendance.menu'), __('History') => null],
        'subtitle' => $from->format('d M Y').' – '.$to->format('d M Y'),
        'actions' => [['url' => route('attendance.board'), 'label' => __('Live Board'), 'icon' => 'M9 19V6m6 13V10M3 19V12m18 7V3']],
    ])

    @php $filterField = 'px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40'; @endphp

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('attendance.report') }}" method="GET" class="flex flex-wrap items-center gap-2 mb-4">
            <input type="date" name="from" value="{{ $from->toDateString() }}" max="{{ today()->toDateString() }}" class="{{ $filterField }}">
            <span class="text-gray-400 text-sm">–</span>
            <input type="date" name="to" value="{{ $to->toDateString() }}" max="{{ today()->toDateString() }}" class="{{ $filterField }}">
            <select name="type" class="{{ $filterField }}">
                <option value="">{{ __('Everyone') }}</option>
                <option value="worker" @selected($type === 'worker')>{{ __('Workers') }}</option>
                <option value="user" @selected($type === 'user')>{{ __('Staff') }}</option>
            </select>
            <select name="branch_id" class="{{ $filterField }}">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2.5 rounded-xl text-sm font-medium bg-brand text-white">{{ __('Show') }}</button>
        </form>

        {{-- ── per person ── --}}
        <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                            <th class="px-4 py-3 font-medium">{{ __('Person') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Days') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Hours') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Avg. Check-in') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Fingerprint') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('No Check-out') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                        @forelse ($summary as $row)
                            <tr class="hover:bg-surface-alt transition-colors {{ $person === $row['key'] ? 'bg-surface-alt' : '' }}">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('attendance.report', array_filter(['from' => $from->toDateString(), 'to' => $to->toDateString(), 'type' => $type, 'branch_id' => $branchId, 'person' => $person === $row['key'] ? null : $row['key']])) }}#log"
                                       class="font-medium text-gray-900 dark:text-white hover:text-brand">{{ $row['person']->name }}</a>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ AttendanceClock::codeOf($row['person']) }} · {{ AttendanceClock::roleOf($row['person']) }}</p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ $row['days'] }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-700 dark:text-gray-200">{{ Attendance::formatMinutes($row['minutes']) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ \Illuminate\Support\Carbon::createFromFormat('H:i', $row['avg_in'])->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ $row['fingerprint'] }}</td>
                                <td class="px-4 py-3 text-right {{ $row['missed'] ? 'text-amber-700 dark:text-amber-300 font-semibold' : 'text-gray-400' }}">{{ $row['missed'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-14 text-center text-sm text-gray-400">{{ __('No attendance in these dates.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── every shift ── --}}
        @if ($log->isNotEmpty())
            <div id="log" class="flex items-center justify-between mt-8 scroll-mt-4">
                <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">
                    {{ $person ? __('Shifts of :name', ['name' => $log->first()->attendable->name]) : __('All Shifts') }}
                </h2>
                @if ($person)
                    <a href="{{ route('attendance.report', array_filter(['from' => $from->toDateString(), 'to' => $to->toDateString(), 'type' => $type, 'branch_id' => $branchId])) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand">{{ __('Show everyone') }}</a>
                @endif
            </div>
            <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

            <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm font-sans">
                        <thead>
                            <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                                <th class="px-4 py-3 font-medium">{{ __('Date') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('Person') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('In') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('Out') }}</th>
                                <th class="px-4 py-3 font-medium text-right">{{ __('Hours') }}</th>
                                <th class="px-4 py-3 font-medium text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                            @foreach ($log as $shift)
                                <tr class="hover:bg-surface-alt transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $shift->work_date->format('D, d M') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $shift->attendable->name }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ AttendanceClock::roleOf($shift->attendable) }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-gray-900 dark:text-white">{{ $shift->check_in_at->format('h:i A') }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $shift->methodLabel($shift->check_in_method) }}{{ $shift->checkedInBy ? ' · '.$shift->checkedInBy->name : '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($shift->check_out_at)
                                            <p class="text-gray-900 dark:text-white">{{ $shift->check_out_at->format('h:i A') }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $shift->methodLabel($shift->check_out_method) }}{{ $shift->checkedOutBy ? ' · '.$shift->checkedOutBy->name : '' }}</p>
                                        @elseif ($shift->missedCheckOut())
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300">{{ __('No check-out') }}</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400">{{ __('Working') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap font-semibold text-gray-900 dark:text-white">{{ Attendance::formatMinutes($shift->minutes()) }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('attendance.edit', $shift) }}" class="inline-flex px-3 py-1.5 rounded-lg text-xs font-medium bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($log->hasPages())
                <div class="mt-4">{{ $log->links() }}</div>
            @endif
        @endif
    </main>
@endsection
