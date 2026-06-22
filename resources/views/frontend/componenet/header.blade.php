    {{-- ────────────────────────── HEADER ────────────────────────── --}}
    <header class="bg-[#004080] text-white px-5 pt-5 pb-4 relative overflow-hidden">
        {{-- decorative circles --}}
        <span class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white/5"></span>
        <span class="absolute top-12 -right-4 w-20 h-20 rounded-full bg-white/5"></span>

        <div class="relative z-10 flex items-center justify-between">
            {{-- logo + greeting --}}
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/img/pageImg/78678687687.png') }}"
                     alt="OptiX"
                     class="w-10 h-10 rounded-xl object-contain bg-white/10 p-1">
                <div>
                    <p class="text-white/60 text-xs font-sans">Welcome to</p>
                    <h1 class="font-heading font-bold text-lg leading-tight tracking-wide">OptiX POS</h1>
                </div>
            </div>
            {{-- logout --}}
            <button class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 active:bg-white/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </div>

        
    </header>