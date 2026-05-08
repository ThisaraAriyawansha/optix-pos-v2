<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>OptiX - Home</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/pageImg/5646546523465 - Copy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-gray-50 min-h-screen relative overflow-x-hidden">

    {{-- ────────────────────────── HEADER ────────────────────────── --}}
    <header class="bg-[#004080] text-white px-5 pt-10 pb-6 relative overflow-hidden">
        {{-- decorative circles --}}
        <span class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white/5"></span>
        <span class="absolute top-12 -right-4 w-20 h-20 rounded-full bg-white/5"></span>

        <div class="relative z-10 flex items-center justify-between mb-5">
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
            {{-- notification bell --}}
            <button class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 active:bg-white/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-400 rounded-full animate-pulse"></span>
            </button>
        </div>

        {{-- search bar --}}
        <div class="relative z-10">
            <div class="flex items-center gap-2 bg-white/10 rounded-2xl px-4 py-3 border border-white/20">
                <svg class="w-4 h-4 text-white/50 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text"
                       placeholder="Search services, repairs…"
                       class="bg-transparent text-white placeholder-white/40 text-sm w-full outline-none font-sans">
            </div>
        </div>
    </header>

 

    {{-- ────────────────────────── BOTTOM NAV ────────────────────────── --}}
    <nav class="fixed bottom-0 w-full bg-white border-t border-gray-100 z-50 shadow-lg">
        <div class="grid grid-cols-4 py-2">

            {{-- Home (active) --}}
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 py-1.5 px-2">
                <div class="w-8 h-8 bg-[#004080] rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-[#004080]">Home</span>
            </a>

            {{-- Services --}}
            <a href="#pricing" class="flex flex-col items-center gap-1 py-1.5 px-2">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-400">Services</span>
            </a>

            {{-- Track --}}
            <a href="#track" class="flex flex-col items-center gap-1 py-1.5 px-2">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13V7m0 0l6-3m-6 3l-6-3m12 16l5.447-2.724A1 1 0 0021 16.382V5.618a1 1 0 00-1.447-.894L15 7m0 13V7"/>
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-400">Track</span>
            </a>

            {{-- Contact --}}
            <a href="#contact" class="flex flex-col items-center gap-1 py-1.5 px-2">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-400">Contact</span>
            </a>

        </div>
    </nav>

</body>
</html>
