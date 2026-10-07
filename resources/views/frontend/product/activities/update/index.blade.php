@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Edit Work Type'),
        'crumbs' => [__('Products') => route('products'), __('Work Rates') => route('products.activities'), $activity->label() => null],
    ])
    @include('frontend.product.activities.update.body')
@endsection
