{{-- ────────────────────────── PRODUCTS BODY ────────────────────────── --}}
@use('App\Support\Money')
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.componenet.alerts')

    <form action="{{ route('products') }}" method="GET" class="mb-4">
        <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('Search by name or code') }}"
               class="w-full sm:w-80 px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40">
    </form>

    @if ($products->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
            <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ $search ? __('No products match your search') : __('No products yet') }}</p>
            @if ($canManage && ! $search)
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('Add raw materials and work rates first, then create a product.') }}</p>
            @endif
        </div>
    @elseif ($canManage)
        <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                            <th class="px-4 py-3 font-medium">{{ __('Product') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Materials') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Labour') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Handling') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Total Cost') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Commission') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Selling Price') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Profit') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                        @foreach ($products as $product)
                            <tr onclick="window.location='{{ route('products.edit', $product) }}'"
                                class="cursor-pointer hover:bg-surface-alt transition-colors {{ $product->is_active ? '' : 'opacity-60' }}">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $product->code }} · {{ __('per :unit', ['unit' => __($product->unit)]) }}</p>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ Money::format($product->material_cost) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ Money::format($product->labour_cost) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ Money::format($product->handling_cost) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ Money::format($product->total_cost) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    {{ Money::format($product->commission_amount) }}
                                    @if ($product->commission_type === 'percent')
                                        <span class="block text-[11px] text-gray-400">{{ Money::qty($product->commission_value) }}%</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ Money::format($product->selling_price) }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap {{ $product->profit() < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    <span class="font-semibold">{{ Money::format($product->profit()) }}</span>
                                    @if (! is_null($product->profitMargin()))
                                        <span class="block text-[11px]">{{ $product->profitMargin() }}%</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                                    <form action="{{ route('products.toggleStatus', $product) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                            {{ $product->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                            {{ $product->is_active ? __('Active') : __('Inactive') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans mt-2">{{ __('All amounts are per unit. Profit = selling price − total cost − commission.') }}</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($products->where('is_active', true) as $product)
                <div class="flex flex-col rounded-2xl bg-surface border border-subtle shadow-sm p-4">
                    <p class="font-medium text-gray-900 dark:text-white tracking-tight truncate">{{ $product->name }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">{{ $product->code }}</p>
                    <p class="font-heading font-semibold text-lg text-gray-900 dark:text-white mt-3">{{ Money::format($product->selling_price) }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">{{ __('per :unit', ['unit' => __($product->unit)]) }}</p>
                </div>
            @endforeach
        </div>
    @endif
</main>
