{{-- ────────────────────────── UPDATE WORK ACTIVITY BODY ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-xl mx-auto">

    @include('frontend.componenet.alerts')

    <form action="{{ route('products.activities.update', $activity) }}" method="POST"
          class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf
        @method('PUT')

        @include('frontend.product.activities.partials.fields')

        <p class="px-4 py-3 rounded-xl bg-surface-alt text-xs text-gray-500 dark:text-gray-400 font-sans">
            {{ __('Changing the default rate does not change existing products. Edit a product to change its rate.') }}
        </p>

        <div class="flex gap-2">
            <a href="{{ route('products.activities') }}" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-center border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200">{{ __('Cancel') }}</a>
            <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">{{ __('Save Changes') }}</button>
        </div>
    </form>
</main>
