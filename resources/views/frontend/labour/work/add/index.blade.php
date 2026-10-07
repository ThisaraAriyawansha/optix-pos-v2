@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Enter Daily Work'),
        'crumbs' => [__('Salary & Work') => route('salary'), __('Daily Work') => route('labour.work', ['date' => $date]), __('Add') => null],
        'subtitle' => __('Record attendance and the work each worker did today'),
        'actions' => [['url' => route('help').'#daily-work', 'label' => __('Help'), 'icon' => \App\Support\Help::ICON]],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('labour.work.store') }}" method="POST">
            @csrf
            @include('frontend.labour.work.partials.form')
        </form>
    </main>
@endsection
