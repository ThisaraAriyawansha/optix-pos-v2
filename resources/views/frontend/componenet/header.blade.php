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
                <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="openLogoutModal()" class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 active:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </div>
        </div>

    </header>

    {{-- ────────────────────────── LOGOUT CONFIRM MODAL ────────────────────────── --}}
    <div id="logoutModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center px-6">
        <div onclick="closeLogoutModal()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-xs bg-surface rounded-2xl shadow-xl border border-subtle p-5 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-surface-alt flex items-center justify-center">
                <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base">Log out of OptiX POS?</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 font-sans">You'll need to sign in again to continue.</p>

            <div class="flex gap-2 mt-5">
                <button type="button" onclick="closeLogoutModal()"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-surface-alt text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                    Cancel
                </button>
                <button type="button" onclick="document.getElementById('logoutForm').submit()"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                    Log out
                </button>
            </div>
        </div>
    </div>

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

        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
    </script>