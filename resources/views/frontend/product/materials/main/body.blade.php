{{-- ────────────────────────── RAW MATERIALS BODY ────────────────────────── --}}
@use('App\Support\Money')
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.componenet.alerts')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

        {{-- ── add material ── --}}
        <form action="{{ route('products.materials.store') }}" method="POST"
              class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
            @csrf
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('New Raw Material') }}</h2>

            @include('frontend.product.materials.partials.fields')

            <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Add Material') }}
            </button>
        </form>

        {{-- ── material cards ── --}}
        <div class="lg:col-span-2">
            @if ($materials->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
                    <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No raw materials yet') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('Create your first material using the form.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($materials as $material)
                        <div class="flex flex-col rounded-2xl bg-surface border border-subtle shadow-sm p-4 {{ $material->is_active ? '' : 'opacity-60' }}">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white tracking-tight truncate">{{ $material->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans line-clamp-2">{{ $material->description ?: __('No description') }}</p>
                                </div>
                                <form action="{{ route('products.materials.toggleStatus', $material) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                        {{ $material->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                        {{ $material->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </div>

                            <div class="flex items-end justify-between mt-4 pt-3 border-t border-gray-100 dark:border-[#1c3350]">
                                <div>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">
                                        {{ trans_choice('Used in :count product|Used in :count products', $material->products_count, ['count' => $material->products_count]) }}
                                    </p>
                                    <p class="font-heading font-semibold text-gray-900 dark:text-white">{{ Money::format($material->unit_cost) }} <span class="text-xs font-sans font-normal text-gray-400">/ {{ $material->unit }}</span></p>
                                </div>
                                <a href="{{ route('products.materials.edit', $material) }}" title="{{ __('Edit') }}"
                                   class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>
