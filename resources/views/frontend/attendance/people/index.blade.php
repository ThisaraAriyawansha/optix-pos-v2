@extends('layouts.frontend')

@use('App\Support\AttendanceClock')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Who Checks In'),
        'crumbs' => [__('Labour') => route('labour'), __('Attendance') => route('attendance.board'), __('Who Checks In') => null],
        'subtitle' => __('Tick the labourers and staff who check in and out. Admins and super admins are not listed.'),
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('attendance.people.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
                @foreach ([
                    ['key' => 'workers', 'title' => __('Labourers'), 'people' => $workers],
                    ['key' => 'users', 'title' => __('Managing Staff'), 'people' => $staff],
                ] as $group)
                    <section class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden" data-group="{{ $group['key'] }}">
                        <div class="flex items-center justify-between gap-2 px-4 py-3 bg-surface-alt border-b border-gray-300 dark:border-[#2a4a70]">
                            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">
                                {{ $group['title'] }}
                                <span class="text-gray-400 font-normal text-sm">(<span class="ticked">{{ $group['people']->where('track_attendance', true)->count() }}</span> / {{ $group['people']->count() }})</span>
                            </h2>
                            <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 accent-[#004080]" onchange="tickAll(this)"
                                       {{ $group['people']->isNotEmpty() && $group['people']->every('track_attendance') ? 'checked' : '' }}>
                                {{ __('Select all') }}
                            </label>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-[#24446a] max-h-[28rem] overflow-y-auto">
                            @forelse ($group['people'] as $person)
                                @php $code = AttendanceClock::codeOf($person); @endphp
                                <li>
                                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-surface-alt">
                                        <input type="checkbox" name="{{ $group['key'] }}[]" value="{{ $person->id }}" class="person-tick w-5 h-5 accent-[#004080]"
                                               onchange="countTicks(this)" @checked($person->track_attendance)>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-medium text-sm text-gray-900 dark:text-white truncate">{{ $person->name }}</span>
                                            <span class="block text-[11px] text-gray-400 dark:text-gray-500 truncate">
                                                {{ $code }} · {{ AttendanceClock::roleOf($person) }}{{ $person->branch ? ' · '.$person->branch->name : '' }}
                                            </span>
                                        </span>
                                        <span class="text-right shrink-0">
                                            <span class="block text-[10px] text-gray-400 dark:text-gray-500">{{ __('Fingerprint ID') }}</span>
                                            <span class="font-heading font-semibold text-sm text-gray-900 dark:text-white">{{ AttendanceClock::devicePin($code) }}</span>
                                        </span>
                                    </label>
                                </li>
                            @empty
                                <li class="px-4 py-10 text-center text-sm text-gray-400">{{ __('Nobody to show.') }}</li>
                            @endforelse
                        </ul>
                    </section>
                @endforeach
            </div>

            <button class="mt-4 w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">{{ __('Save') }}</button>
        </form>

        {{-- ── fingerprint device ── --}}
        <section class="mt-8 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Fingerprint Machine') }}</h2>
            <ol class="mt-3 space-y-1.5 text-sm text-gray-600 dark:text-gray-300 list-decimal list-inside">
                <li>{{ __('On the machine, add each person with the "Fingerprint ID" shown above as their user ID, and scan their finger.') }}</li>
                <li>{{ __('In the machine\'s Cloud / ADMS server settings, enter this server address:') }}
                    <code class="px-1.5 py-0.5 rounded bg-surface-alt text-gray-900 dark:text-white">{{ request()->getHost() }}</code>
                    {{ __('port') }} <code class="px-1.5 py-0.5 rounded bg-surface-alt text-gray-900 dark:text-white">{{ request()->getPort() }}</code></li>
                <li>{{ __('Give the machine\'s serial number to your system technician to allow it.') }}
                    @if (config('attendance.device_serials'))
                        <span class="text-green-700 dark:text-green-400">{{ __('Allowed:') }} {{ implode(', ', config('attendance.device_serials')) }}</span>
                    @else
                        <span class="text-amber-700 dark:text-amber-300">{{ __('No machine allowed yet.') }}</span>
                    @endif
                </li>
                <li>{{ __('Each finger scan checks the person in; the next scan checks them out.') }}</li>
            </ol>

            <h3 class="font-medium text-sm text-gray-900 dark:text-white mt-5">{{ __('Latest scans received') }}</h3>
            @if ($punches->isEmpty())
                <p class="text-sm text-gray-400 mt-2">{{ __('No scans received yet.') }}</p>
            @else
                <ul class="mt-2 divide-y divide-gray-200 dark:divide-[#24446a] text-sm">
                    @foreach ($punches as $punch)
                        <li class="flex items-center justify-between gap-2 py-2">
                            <span class="text-gray-700 dark:text-gray-200">{{ __('ID') }} {{ $punch->pin }} · {{ $punch->device_sn }}</span>
                            <span class="text-xs {{ $punch->result === 'unknown' ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ ['check_in' => __('Checked in'), 'check_out' => __('Checked out'), 'duplicate' => __('Double scan ignored'), 'unknown' => __('Unknown ID')][$punch->result] ?? $punch->result }}
                                · {{ $punch->punched_at->format('d M h:i A') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </main>

    <script>
        function tickAll(box) {
            const group = box.closest('[data-group]');
            group.querySelectorAll('.person-tick').forEach((tick) => tick.checked = box.checked);
            countTicks(box);
        }

        function countTicks(input) {
            const group = input.closest('[data-group]');
            group.querySelector('.ticked').textContent = group.querySelectorAll('.person-tick:checked').length;
        }
    </script>
@endsection
