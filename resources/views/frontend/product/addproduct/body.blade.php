{{-- ────────────────────────── ADD PRODUCT BODY ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.componenet.alerts')

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        @include('frontend.product.partials.form')
    </form>
</main>
