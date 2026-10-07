@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Add Product'),
        'crumbs' => [__('Products') => route('products'), __('Add Product') => null],
        'subtitle' => __('Build the cost from materials, labour and handling, then set the selling price'),
    ])
    @include('frontend.product.addproduct.body')
@endsection
