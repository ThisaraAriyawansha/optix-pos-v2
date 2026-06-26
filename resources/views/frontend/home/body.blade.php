{{-- ────────────────────────── QUICK ACTIONS ────────────────────────── --}}
    <main class="px-5 pt-6 pb-28 max-w-6xl mx-auto">

        <div class="flex items-center justify-between">
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight">Main Menu</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-sans">Tap to open</span>
        </div>
        <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-6"></div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">

            {{-- New Sale --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-brand text-white shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white/10 flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight">New Sale</span>
                    <span class="text-[11px] text-white/60">Start a transaction</span>
                </span>
            </a>

            {{-- Products / Inventory --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Products</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Manage inventory</span>
                </span>
            </a>

            {{-- Orders --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7a2 2 0 012-2h6a2 2 0 012 2v10m-10 0a2 2 0 002 2h6a2 2 0 002-2m-10 0H5a2 2 0 01-2-2V9a2 2 0 012-2h2"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Orders</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Track orders</span>
                </span>
            </a>

            {{-- Suppliers --}}
            <a href="{{ route('suppliers') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V20a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5H10v5a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Suppliers</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Manage suppliers</span>
                </span>
            </a>

            {{-- Customers --}}
            <a href="{{ route('customers') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Customers</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Customer records</span>
                </span>
            </a>

            {{-- User Management --}}
            <a href="{{ route('users') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Users</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Manage accounts</span>
                </span>
            </a>

            {{-- Project Management --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 8l2 2 4-4"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Projects</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Track projects</span>
                </span>
            </a>

            {{-- Reports --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6m6 13V10M3 19V12m18 7V3"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Reports</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">View analytics</span>
                </span>
            </a>

            {{-- Repairs / Services --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.77z"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Repairs</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Service requests</span>
                </span>
            </a>

            {{-- Expenses --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m0-12a8 8 0 100 16 8 8 0 000-16z"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Expenses</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Track spending</span>
                </span>
            </a>

            {{-- Branches --}}
            <a href="{{ route('branches') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5a1 1 0 011-1h6a1 1 0 011 1v16M13 21V9a1 1 0 011-1h5a1 1 0 011 1v12M9 7h.01M9 11h.01M9 15h.01"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Branches</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">Manage locations</span>
                </span>
            </a>

            {{-- Settings --}}
            <a href="#" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="flex flex-col items-center gap-0.5">
                    <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Settings</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500">App preferences</span>
                </span>
            </a>

        </div>
    </main>
