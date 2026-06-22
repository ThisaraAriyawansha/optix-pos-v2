{{-- ────────────────────────── BOTTOM NAV ────────────────────────── --}}
<nav class="fixed bottom-2 sm:bottom-3 inset-x-0 z-50 flex justify-center px-2 sm:px-3">
    <div class="grid grid-cols-5 items-center px-1.5 sm:px-3 py-1 sm:py-1.5 rounded-[24px] sm:rounded-[28px] w-full max-w-md
                bg-white/20 backdrop-blur-xl backdrop-saturate-150
                border border-white/40
                shadow-[0_8px_30px_rgba(0,0,0,0.12)]">

        {{-- Back --}}
        <a href="javascript:history.back()"
           class="flex flex-col items-center justify-center gap-0.5 py-1 sm:py-1.5 rounded-2xl active:bg-white/40 transition-colors">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="text-[9px] sm:text-[10px] font-medium text-gray-500 whitespace-nowrap">Back</span>
        </a>

        {{-- Scan --}}
        <a href="#" class="flex flex-col items-center justify-center gap-0.5 py-1 sm:py-1.5 rounded-2xl active:bg-white/40 transition-colors">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2M3 17v2a2 2 0 002 2h2m10-16h2a2 2 0 012 2v2m-4 12h2a2 2 0 002-2v-2M7 12h10"/>
            </svg>
            <span class="text-[9px] sm:text-[10px] font-medium text-gray-500 whitespace-nowrap">Scan</span>
        </a>

        {{-- Home --}}
        <a href="{{ route('home') }}" class="flex items-center justify-center">
            <span class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-brand flex items-center justify-center shadow-md ring-2 sm:ring-4 ring-white/60 active:scale-95 transition-transform">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </span>
        </a>

        {{-- Reports --}}
        <a href="#" class="flex flex-col items-center justify-center gap-0.5 py-1 sm:py-1.5 rounded-2xl active:bg-white/40 transition-colors">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6m6 13V10M3 19V12m18 7V3"/>
            </svg>
            <span class="text-[9px] sm:text-[10px] font-medium text-gray-500 whitespace-nowrap">Reports</span>
        </a>

        {{-- Sale --}}
        <a href="#" class="flex flex-col items-center justify-center gap-0.5 py-1 sm:py-1.5 rounded-2xl active:bg-white/40 transition-colors">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-[9px] sm:text-[10px] font-semibold text-brand whitespace-nowrap">Sale</span>
        </a>

    </div>
</nav>
