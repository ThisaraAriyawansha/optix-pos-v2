{{-- ────────────────────────── EXPENSE TYPES HERO ────────────────────────── --}}
<section class="px-5 pt-6 max-w-6xl mx-auto">

    {{-- breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs font-sans text-gray-400 dark:text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-brand transition-colors">{{ __('Home') }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('expenses') }}" class="hover:text-brand transition-colors">{{ __('Expenses') }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 dark:text-gray-300">{{ __('Expense Types') }}</span>
    </nav>

    <h1 class="font-heading font-semibold text-gray-900 dark:text-white text-xl tracking-tight mt-3">{{ __('Expense Types') }}</h1>
    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-0.5">{{ __('Group your spending — rent, bills, salaries and more.') }}</p>
</section>
