@extends('layouts.frontend')

@use('App\Support\AttendanceClock')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Correct Attendance'),
        'crumbs' => [__('Attendance') => route('attendance.menu'), __('History') => route('attendance.report'), __('Edit') => null],
        'subtitle' => $attendance->attendable->name.' · '.AttendanceClock::roleOf($attendance->attendable),
    ])

    @php
        $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
        $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    @endphp

    <main class="px-5 pt-5 pb-28 max-w-xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('attendance.update', $attendance) }}" method="POST" class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="check_in_at" class="{{ $label }}">{{ __('Check-in time') }} <span class="text-accent">*</span></label>
                <input type="datetime-local" name="check_in_at" id="check_in_at" required class="{{ $field }}"
                       value="{{ old('check_in_at', $attendance->check_in_at->format('Y-m-d\TH:i')) }}">
                <p class="text-[11px] text-gray-400 mt-1">{{ $attendance->methodLabel($attendance->check_in_method) }}</p>
            </div>
            <div>
                <label for="check_out_at" class="{{ $label }}">{{ __('Check-out time') }}</label>
                <input type="datetime-local" name="check_out_at" id="check_out_at" class="{{ $field }}"
                       value="{{ old('check_out_at', $attendance->check_out_at?->format('Y-m-d\TH:i')) }}">
                <p class="text-[11px] text-gray-400 mt-1">{{ __('Leave empty if still working.') }}</p>
            </div>
            <div>
                <label for="notes" class="{{ $label }}">{{ __('Notes') }}</label>
                <textarea name="notes" id="notes" rows="2" maxlength="1000" class="{{ $field }} resize-none" placeholder="{{ __('Why was it changed?') }}">{{ old('notes', $attendance->notes) }}</textarea>
            </div>
            <button class="w-full py-3 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">{{ __('Save') }}</button>
        </form>

        <form action="{{ route('attendance.destroy', $attendance) }}" method="POST" class="mt-3"
              onsubmit="return confirm(@js(__('Delete this attendance record?')))">
            @csrf
            @method('DELETE')
            <button class="w-full py-2.5 rounded-xl text-sm font-semibold text-red-600 border border-red-200 dark:border-red-900/40">{{ __('Delete Record') }}</button>
        </form>
    </main>
@endsection
