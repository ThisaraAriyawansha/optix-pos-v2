{{-- ────────────────────────── SETTINGS HUB BODY ────────────────────────── --}}
<main class="px-5 pt-6 pb-28 max-w-6xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-sans">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">

        {{-- Manage Profile --}}
        <a href="{{ route('settings.profile') }}" class="group relative flex flex-col items-center justify-center gap-3 h-32 sm:h-36 lg:h-40 rounded-2xl bg-brand text-white shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white/10 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 9a3 3 0 11-6 0 3 3 0 016 0zm6 3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="flex flex-col items-center gap-0.5">
                <span class="font-medium text-sm sm:text-[15px] tracking-tight">{{ __('Manage Profile') }}</span>
                <span class="text-[11px] text-white/60">{{ __('Update your personal details') }}</span>
            </span>
        </a>

    </div>
</main>
