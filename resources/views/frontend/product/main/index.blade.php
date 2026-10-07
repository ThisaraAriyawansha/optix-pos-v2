@extends('layouts.frontend')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Products'),
        'crumbs' => [__('Products') => null],
        'subtitle' => $canManage ? __('Costs, commission and selling prices') : __('Product list and selling prices'),
        'actions' => $canManage ? [
            ['url' => route('help').'#admin', 'label' => __('Help'), 'icon' => \App\Support\Help::ICON],
            ['url' => route('products.materials'), 'label' => __('Raw Materials'), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['url' => route('products.activities'), 'label' => __('Work Rates'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['url' => route('products.create'), 'label' => __('Add Product'), 'primary' => true],
        ] : [],
    ])
    @include('frontend.product.main.body')
@endsection
