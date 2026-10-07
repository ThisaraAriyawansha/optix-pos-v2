@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Edit Raw Material'),
        'crumbs' => [__('Products') => route('products'), __('Raw Materials') => route('products.materials'), $material->name => null],
    ])
    @include('frontend.product.materials.update.body')
@endsection
