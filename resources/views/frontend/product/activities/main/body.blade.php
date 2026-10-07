{{-- ────────────────────────── WORK ACTIVITIES BODY ────────────────────────── --}}
@use('App\Support\Money')
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.componenet.alerts')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

        {{-- ── add activity ── --}}
        <form action="{{ route('products.activities.store') }}" method="POST"
              class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
            @csrf
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('New Work Type') }}</h2>

            @include('frontend.product.activities.partials.fields')

            <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Add Work Type') }}
            </button>
        </form>

        {{-- ── activity cards ── --}}
        <div class="lg:col-span-2">
            @if ($activities->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
                    <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No work types yet') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($activities as $activity)
                        <div class="flex flex-col rounded-2xl bg-surface border border-subtle shadow-sm p-4 {{ $activity->is_active ? '' : 'opacity-60' }}">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white tracking-tight truncate">{{ $activity->label() }}</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $activity->cost_group === 'handling' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-300' }}">
                                            {{ $activity->costGroupLabel() }}
                                        </span>
                                        @if ($activity->is_production)
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300">{{ __('Produced') }}</span>
                                        @endif
                                        @if ($activity->is_dispatch)
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-600 dark:bg-purple-500/15 dark:text-purple-300">{{ __('Dispatched') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <form action="{{ route('products.activities.toggleStatus', $activity) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                        {{ $activity->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                        {{ $activity->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </div>

                            <div class="flex items-end justify-between mt-4 pt-3 border-t border-gray-100 dark:border-[#1c3350]">
                                <div>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">
                                        {{ __('Default rate') }} · {{ trans_choice(':count product|:count products', $activity->products_count, ['count' => $activity->products_count]) }}
                                    </p>
                                    <p class="font-heading font-semibold text-gray-900 dark:text-white">{{ Money::format($activity->default_rate) }} <span class="text-xs font-sans font-normal text-gray-400">/ {{ __('unit') }}</span></p>
                                </div>
                                <a href="{{ route('products.activities.edit', $activity) }}" title="{{ __('Edit') }}"
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
