{{-- ────────────────────────── UPDATE RAW MATERIAL BODY ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-xl mx-auto">

    @include('frontend.componenet.alerts')

    <form action="{{ route('products.materials.update', $material) }}" method="POST"
          class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf
        @method('PUT')

        @include('frontend.product.materials.partials.fields')

        @if ($material->products->isNotEmpty())
            <div class="px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 text-xs font-sans">
                {{ __('Saving will recalculate the cost of:') }}
                <span class="font-medium">{{ $material->products->pluck('name')->implode(', ') }}</span>
            </div>
        @endif

        <div class="flex gap-2">
            <a href="{{ route('products.materials') }}" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-center border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200">{{ __('Cancel') }}</a>
            <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">{{ __('Save Changes') }}</button>
        </div>
    </form>
</main>
