{{-- ────────────────────────── EXPENSES HUB HERO ────────────────────────── --}}
<section class="px-5 pt-6 max-w-6xl mx-auto">

    {{-- breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs font-sans text-gray-400 dark:text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-brand transition-colors">{{ __('Home') }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 dark:text-gray-300">{{ __('Expenses') }}</span>
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-3 mt-3">
        <div>
            <h1 class="font-heading font-semibold text-gray-900 dark:text-white text-xl tracking-tight">{{ __('Expenses') }}</h1>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-0.5">{{ now()->format('F Y') }}</p>
        </div>

        <div class="flex items-center gap-2">
            {{-- branch filter --}}
            <form action="{{ route('expenses') }}" method="GET">
                <select name="branch_id" onchange="this.form.submit()"
                        class="px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40">
                    <option value="">{{ __('All branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('expenses.create') }}"
               class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-brand text-white text-sm font-medium active:scale-95 transition-transform whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add Expense') }}
            </a>
        </div>
    </div>
</section>
