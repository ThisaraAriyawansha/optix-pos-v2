{{-- ────────────────────────── USERS HUB BODY ────────────────────────── --}}
<main class="px-5 pt-6 pb-28 max-w-6xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-sans">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">

        {{-- Manage Roles --}}
        <a href="{{ route('roles.manage') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-brand text-white shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white/10 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="flex flex-col items-center gap-0.5">
                <span class="font-medium text-sm sm:text-[15px] tracking-tight">Manage Roles</span>
                <span class="text-[11px] text-white/60">View &amp; add roles</span>
            </span>
        </a>

        {{-- Manage Users --}}
        <a href="{{ route('users.manage') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-surface shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 border border-subtle">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-alt flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="flex flex-col items-center gap-0.5">
                <span class="font-medium text-sm sm:text-[15px] tracking-tight text-gray-900 dark:text-white">Manage Users</span>
                <span class="text-[11px] text-gray-400 dark:text-gray-500">View &amp; add users</span>
            </span>
        </a>

    </div>
</main>
