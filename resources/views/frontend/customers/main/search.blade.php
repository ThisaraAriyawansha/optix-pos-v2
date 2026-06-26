{{-- ────────────────────────── CUSTOMER SEARCH ────────────────────────── --}}
<section class="px-5 pt-4 max-w-6xl mx-auto">
    <form action="{{ route('customers') }}" method="GET" class="flex items-center gap-2">
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by name, code, phone or email"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
        </div>

        <button type="submit"
                class="px-4 py-2.5 rounded-xl bg-brand text-white text-sm font-medium active:scale-95 transition-transform">
            Search
        </button>

        @if ($search)
            <a href="{{ route('customers') }}"
               class="px-4 py-2.5 rounded-xl bg-surface-alt border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium active:scale-95 transition-transform">
                Clear
            </a>
        @endif
    </form>
</section>
