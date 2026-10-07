{{-- ────────────────────────── UPDATE PRODUCT BODY ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.componenet.alerts')

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        @include('frontend.product.partials.form')
    </form>
</main>
