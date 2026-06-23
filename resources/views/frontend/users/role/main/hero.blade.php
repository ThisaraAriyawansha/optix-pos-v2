{{-- ────────────────────────── MANAGE ROLES HERO ────────────────────────── --}}
<section class="px-5 pt-6 max-w-6xl mx-auto">

    {{-- breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs font-sans text-gray-400 dark:text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-brand transition-colors">Home</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('users') }}" class="hover:text-brand transition-colors">Users</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 dark:text-gray-300">Manage Roles</span>
    </nav>

    <div class="flex items-center justify-between mt-3">
        <h1 class="font-heading font-semibold text-gray-900 dark:text-white text-xl tracking-tight">Manage Roles</h1>

        <a href="{{ route('roles.create') }}"
           class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-brand text-white text-sm font-medium active:scale-95 transition-transform">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Role
        </a>
    </div>
</section>
