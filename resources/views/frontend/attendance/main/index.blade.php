@extends('layouts.frontend')

@use('App\Models\Attendance')
@use('App\Support\AttendanceClock')
@use('App\Support\Money')

@section('content')
    @php
        $actions = [['url' => route('help').'#attendance', 'label' => __('Help'), 'icon' => \App\Support\Help::ICON]];
        if (auth()->user()->can('manage-attendance')) {
            $actions[] = ['url' => route('attendance.board'), 'label' => __('Live Board'), 'icon' => 'M9 19V6m6 13V10M3 19V12m18 7V3', 'primary' => true];
        }
    @endphp
    @include('frontend.componenet.pagehero', [
        'title' => __('Check In / Check Out'),
        'crumbs' => [__('Attendance') => route('attendance.menu'), __('Check In / Check Out') => null],
        'subtitle' => now()->translatedFormat('l, d F Y'),
        'actions' => $actions,
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        @php $filterField = 'px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40'; @endphp

        {{-- ── summary ── --}}
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl bg-brand text-white p-4 shadow-sm">
                <p class="text-xs text-white/70 font-sans">{{ __('Working Now') }}</p>
                <p class="font-heading font-semibold text-2xl mt-1">{{ $summary['in'] }}</p>
                <p class="text-[11px] text-white/70 font-sans mt-1">{{ __(':count labourers', ['count' => $summary['labourers_in']]) }}</p>
            </div>
            <div class="rounded-2xl bg-surface border border-subtle p-4 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Not In Yet') }}</p>
                <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white mt-1">{{ $summary['absent'] }}</p>
            </div>
            <div class="rounded-2xl bg-surface border border-subtle p-4 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Gone Home') }}</p>
                <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white mt-1">{{ $summary['out'] }}</p>
            </div>
        </div>

        {{-- ── filters ── --}}
        <form action="{{ route('attendance') }}" method="GET" class="flex flex-wrap items-center gap-2 mt-4 mb-4">
            <div class="flex rounded-xl border border-gray-300 dark:border-[#2a4a70] overflow-hidden text-sm font-medium">
                @foreach (['' => __('Everyone'), 'worker' => __('Labourers'), 'user' => __('Staff')] as $value => $tabLabel)
                    <a href="{{ route('attendance', array_filter(['type' => $value, 'branch_id' => $branchId])) }}"
                       class="px-3.5 py-2.5 {{ (string) $type === (string) $value ? 'bg-brand text-white' : 'bg-surface text-gray-600 dark:text-gray-300' }}">{{ $tabLabel }}</a>
                @endforeach
            </div>
            <input type="hidden" name="type" value="{{ $type }}">
            <select name="branch_id" onchange="this.form.submit()" class="{{ $filterField }}">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
            <input type="search" id="person-search" placeholder="{{ __('Search name or ID') }}" oninput="filterPeople(this.value)"
                   class="{{ $filterField }} flex-1 min-w-[10rem]">
        </form>

        {{-- ── people ── --}}
        @if ($rows->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-14 rounded-2xl bg-surface border border-subtle">
                <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('Nobody is set to check in') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('An admin chooses who checks in under "Who Checks In".') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($rows as $row)
                    @php
                        $person = $row['person'];
                        $code = AttendanceClock::codeOf($person);
                        $last = $row['shifts']->last();
                    @endphp
                    <div class="person-card rounded-2xl bg-surface border shadow-sm p-4 flex flex-col gap-3
                                {{ $row['status'] === 'in' ? 'border-green-300 dark:border-green-700/60' : 'border-subtle' }}"
                         data-search="{{ strtolower($person->name.' '.$code) }}">
                        <div class="flex items-start gap-3">
                            <span class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center font-heading font-semibold text-sm
                                         {{ $row['type'] === 'worker' ? 'bg-[#2f6fb8]/10 text-[#2f6fb8] dark:bg-[#4a8ad4]/15 dark:text-[#8bb8ea]' : 'bg-[#c77d12]/10 text-[#9a5f0a] dark:bg-[#c07f1c]/15 dark:text-[#e0b066]' }}">
                                {{ mb_strtoupper(mb_substr($person->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $person->name }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate">{{ $code }} · {{ AttendanceClock::roleOf($person) }}</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium
                                {{ match ($row['status']) {
                                    'in' => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400',
                                    'out' => 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300',
                                    default => 'bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300',
                                } }}">
                                {{ ['in' => __('Working'), 'out' => __('Checked out'), 'absent' => __('Not in')][$row['status']] }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400 font-sans min-h-[2rem]">
                            @if ($row['status'] === 'in')
                                <p>
                                    {{ __('In at') }} <span class="font-semibold text-gray-900 dark:text-white">{{ $row['open']->check_in_at->format('h:i A') }}</span>
                                    @if ($row['open']->check_in_method === 'fingerprint')
                                        <span title="{{ __('Fingerprint') }}">· {{ __('Fingerprint') }}</span>
                                    @endif
                                </p>
                                <p>{{ __('Worked') }} <span class="font-semibold text-gray-900 dark:text-white">{{ Attendance::formatMinutes($row['minutes']) }}</span></p>
                            @elseif ($row['status'] === 'out')
                                <p>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $row['shifts']->first()->check_in_at->format('h:i A') }}</span>
                                    → <span class="font-semibold text-gray-900 dark:text-white">{{ $last->check_out_at->format('h:i A') }}</span>
                                    · {{ Attendance::formatMinutes($row['minutes']) }}
                                </p>
                                @if ($row['type'] === 'worker')
                                    @php $entry = $row['workEntry']; @endphp
                                    @if ($entry && $entry->items->isNotEmpty())
                                        <p>{{ __('Work saved') }} · {{ $entry->attendanceLabel() }}</p>
                                    @else
                                        <a href="{{ route('labour.work.create', ['worker' => $person->id, 'date' => today()->toDateString()]) }}" class="text-brand underline">{{ __('Add work done') }}</a>
                                    @endif
                                @endif
                            @else
                                <p class="text-gray-400">{{ __('Not checked in today') }}</p>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @if ($row['status'] === 'in')
                                @if ($row['type'] === 'worker')
                                    <a href="{{ route('attendance.checkOut.form', $row['open']) }}"
                                       class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold bg-red-600 text-white active:scale-95 transition-transform">{{ __('Check Out') }}</a>
                                @else
                                    <form action="{{ route('attendance.checkOut', $row['open']) }}" method="POST" class="flex-1"
                                          data-confirm="{{ __('Check out :name now?', ['name' => $person->name]) }}"
                                          data-confirm-title="{{ __('Check Out') }}" data-confirm-ok="{{ __('Check Out') }}" data-confirm-variant="danger">
                                        @csrf
                                        <button class="w-full py-2.5 rounded-xl text-sm font-semibold bg-red-600 text-white active:scale-95 transition-transform">{{ __('Check Out') }}</button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('attendance.checkIn') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $row['type'] }}">
                                    <input type="hidden" name="id" value="{{ $person->id }}">
                                    <button class="w-full py-2.5 rounded-xl text-sm font-semibold active:scale-95 transition-transform
                                                   {{ $row['status'] === 'absent' ? 'bg-green-600 text-white' : 'border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200' }}">
                                        {{ $row['status'] === 'absent' ? __('Check In') : __('Check In Again') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <p id="no-match" class="hidden text-center text-sm text-gray-400 py-10">{{ __('No one matches your search.') }}</p>
        @endif
    </main>

    <script>
        function filterPeople(term) {
            term = term.trim().toLowerCase();
            let shown = 0;
            document.querySelectorAll('.person-card').forEach((card) => {
                const match = !term || card.dataset.search.includes(term);
                card.classList.toggle('hidden', !match);
                shown += match ? 1 : 0;
            });
            document.getElementById('no-match')?.classList.toggle('hidden', shown > 0);
        }
    </script>
@endsection
