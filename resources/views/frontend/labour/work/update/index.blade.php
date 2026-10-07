@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Edit Daily Work'),
        'crumbs' => [__('Salary & Work') => route('salary'), __('Daily Work') => route('labour.work', ['date' => $date, 'branch_id' => $entry->branch_id]), $entry->worker->name => null],
        'subtitle' => $entry->work_date->translatedFormat('l, d F Y'),
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        @if ($entry->isPaid())
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 text-sm font-sans">
                <span>{{ __('This work is already paid in :code, so it is locked.', ['code' => $entry->salaryPayment->payment_code]) }}</span>
                <a href="{{ route('labour.salary.show', $entry->salaryPayment) }}" class="font-medium underline">{{ __('View payslip') }}</a>
            </div>
        @endif

        <form action="{{ route('labour.work.update', $entry) }}" method="POST">
            @csrf
            @method('PUT')
            <fieldset {{ $entry->isPaid() ? 'disabled' : '' }} class="{{ $entry->isPaid() ? 'opacity-70' : '' }}">
                @include('frontend.labour.work.partials.form')
            </fieldset>
        </form>

        @unless ($entry->isPaid())
            <form action="{{ route('labour.work.destroy', $entry) }}" method="POST" class="mt-4"
                  onsubmit="return confirm(@js(__('Delete this work entry?')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">{{ __('Delete this entry') }}</button>
            </form>
        @endunless
    </main>
@endsection
