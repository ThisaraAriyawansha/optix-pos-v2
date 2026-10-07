@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Add Worker'),
        'crumbs' => [__('Workers') => route('labour'), __('All Workers') => route('labour.workers'), __('Add') => null],
        'subtitle' => __('Labourers do not log in — their work is entered by staff'),
    ])

    <main class="px-5 pt-5 pb-28 max-w-3xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('labour.workers.store') }}" method="POST"
              class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
            @csrf
            @include('frontend.labour.workers.partials.form')

            <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Save Worker') }}
            </button>
        </form>
    </main>
@endsection
