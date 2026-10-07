@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => $product->name,
        'crumbs' => [__('Products') => route('products'), $product->code => null],
        'subtitle' => __('Edit costs and selling price'),
    ])
    @include('frontend.product.updateproduct.body')
@endsection
