@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Work Types & Rates'),
        'crumbs' => [__('Products') => route('products'), __('Work Rates') => null],
        'subtitle' => __('Work that labourers are paid for per unit — making, sorting, transport, loading'),
    ])
    @include('frontend.product.activities.main.body')
@endsection
