    {{-- ────────────────────────── HEADER ────────────────────────── --}}
    <header class="bg-brand text-white px-5 pt-5 pb-4 relative overflow-hidden">
        {{-- decorative circles --}}
        <span class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white/5"></span>
        <span class="absolute top-12 -right-4 w-20 h-20 rounded-full bg-white/5"></span>

        <div class="relative z-10 flex flex-wrap items-center justify-between gap-y-2 sm:grid sm:grid-cols-3 sm:gap-0">
            {{-- logo + greeting --}}
            <div class="flex items-center gap-3 order-1 justify-self-start">
                <img src="{{ asset('assets/img/pageImg/78678687687.png') }}"
                     alt="OptiX"
                     class="w-10 h-10 rounded-xl object-contain bg-white/10 p-1 shrink-0">
                <div>
                    <p class="text-white/60 text-xs font-sans">Welcome to</p>
                    <h1 class="font-heading font-bold text-lg leading-tight tracking-wide">OptiX POS</h1>
                </div>
            </div>

            {{-- live date + time --}}
            <div class="w-full sm:w-auto flex flex-col items-center justify-center order-3 sm:order-2 justify-self-center">
                <p id="liveTime" class="font-heading font-bold text-sm sm:text-base leading-tight tracking-wide tabular-nums whitespace-nowrap">--:--:--</p>
                <p id="liveDate" class="text-white/60 text-[10px] sm:text-[11px] font-sans whitespace-nowrap">&nbsp;</p>
            </div>

            <div class="flex items-center gap-2 order-2 sm:order-3 justify-self-end">
                {{-- theme toggle --}}
                <button onclick="toggleTheme()" class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 active:bg-white/20 transition-colors">
                    {{-- sun icon (shown in dark mode) --}}
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{-- moon icon (shown in light mode) --}}
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 1020.354 15.354z"/>
                    </svg>
                </button>
                {{-- logout --}}
                <button class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 active:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </div>
        </div>

    </header>

    <script>
        function updateLiveClock() {
            const now = new Date();
            const timeEl = document.getElementById('liveTime');
            const dateEl = document.getElementById('liveDate');
            if (timeEl) {
                timeEl.textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            }
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('en-US', {
                    weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'
                });
            }
        }
        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    </script>