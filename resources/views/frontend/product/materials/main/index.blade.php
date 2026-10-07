@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Raw Materials'),
        'crumbs' => [__('Products') => route('products'), __('Raw Materials') => null],
        'subtitle' => __('Each material keeps its own cost. Changing a cost updates every product that uses it.'),
    ])
    @include('frontend.product.materials.main.body')
@endsection
