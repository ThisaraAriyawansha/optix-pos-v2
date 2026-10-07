@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => $worker->name,
        'crumbs' => [__('Labour') => route('labour'), __('Workers') => route('labour.workers'), $worker->code => null],
        'actions' => [
            ['url' => route('labour.salary.create', ['worker' => $worker->id]), 'label' => __('Pay Salary'), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ],
    ])

    <main class="px-5 pt-5 pb-28 max-w-3xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('labour.workers.update', $worker) }}" method="POST"
              class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
            @csrf
            @method('PUT')
            @include('frontend.labour.workers.partials.form')

            <div class="flex gap-2">
                <a href="{{ route('labour.workers') }}" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-center border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200">{{ __('Cancel') }}</a>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">{{ __('Save Changes') }}</button>
            </div>
        </form>
    </main>
@endsection
