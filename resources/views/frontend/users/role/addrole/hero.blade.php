{{-- ────────────────────────── ADD USER ROLE HERO ────────────────────────── --}}
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
        <a href="{{ route('roles.manage') }}" class="hover:text-brand transition-colors">Manage Roles</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 dark:text-gray-300">Add User Role</span>
    </nav>

    <h1 class="font-heading font-semibold text-gray-900 dark:text-white text-xl tracking-tight mt-3">Add User Role</h1>
</section>
