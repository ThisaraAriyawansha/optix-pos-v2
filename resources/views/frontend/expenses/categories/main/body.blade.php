{{-- ────────────────────────── EXPENSE TYPES BODY ────────────────────────── --}}
@use('App\Models\Expense')
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @include('frontend.expenses.partials.alerts')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

        {{-- ── add type ── --}}
        <form id="add-type" action="{{ route('expenses.categories.store') }}" method="POST"
              class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5 scroll-mt-4">
            @csrf
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('New Expense Type') }}</h2>

            @include('frontend.expenses.categories.partials.fields')

            <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Add Type') }}
            </button>
        </form>

        {{-- ── type cards ── --}}
        <div class="lg:col-span-2">
            @if ($categories->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
                    <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No expense types yet') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('Create your first type using the form.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($categories as $category)
                        <div class="flex flex-col rounded-2xl bg-surface border border-subtle shadow-sm p-4 {{ $category->is_active ? '' : 'opacity-60' }}">
                            <div class="flex items-start gap-3">
                                @include('frontend.expenses.partials.icon', ['category' => $category, 'size' => 'lg'])
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white tracking-tight truncate">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans line-clamp-2">{{ $category->description ?: __('No description') }}</p>
                                </div>
                                <form action="{{ route('expenses.categories.toggleStatus', $category) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                        {{ $category->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                        {{ $category->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </div>

                            <div class="flex items-end justify-between mt-4 pt-3 border-t border-gray-100 dark:border-[#1c3350]">
                                <div>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">
                                        {{ trans_choice(':count entry|:count entries', $category->expenses_count, ['count' => $category->expenses_count]) }} · {{ __('all time') }}
                                    </p>
                                    <p class="font-heading font-semibold text-gray-900 dark:text-white">{{ Expense::money($category->expenses_sum_amount) }}</p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if ($category->is_active)
                                        <a href="{{ route('expenses.create', ['category' => $category->id]) }}" title="{{ __('Add expense') }}"
                                           class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('expenses.list', ['category' => $category->id]) }}" title="{{ __('View records') }}"
                                       class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
                                    </a>
                                    <a href="{{ route('expenses.categories.edit', $category) }}" title="{{ __('Edit') }}"
                                       class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>
