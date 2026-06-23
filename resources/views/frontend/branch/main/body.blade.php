{{-- ────────────────────────── BRANCH LIST BODY ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-sans">
            {{ session('success') }}
        </div>
    @endif

    @if ($branches->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
            <div class="w-12 h-12 rounded-full bg-surface-alt flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5a1 1 0 011-1h6a1 1 0 011 1v16M13 21V9a1 1 0 011-1h5a1 1 0 011 1v12M9 7h.01M9 11h.01M9 15h.01"/>
                </svg>
            </div>
            <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">No branches yet</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">Add your first branch to get started.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($branches as $branch)
                <div class="rounded-2xl bg-surface border border-subtle p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">
                            {{ $branch->name }}
                        </h3>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('branches.edit', $branch) }}"
                               class="w-7 h-7 flex items-center justify-center rounded-lg bg-surface-alt text-gray-500 dark:text-gray-400 active:scale-90 transition-transform">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('branches.toggleStatus', $branch) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                    {{ $branch->status ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                    {{ $branch->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($branch->description)
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1.5 line-clamp-2">
                            {{ $branch->description }}
                        </p>
                    @endif

                    <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] my-3"></div>

                    <div class="space-y-1.5 text-sm font-sans text-gray-700 dark:text-gray-300">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $branch->address }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.25 1.035l-1.21 1.21a11.042 11.042 0 005.516 5.516l1.21-1.21a1 1 0 011.035-.25l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $branch->main_contact }}</span>
                        </div>
                        @if ($branch->secondary_contact)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.25 1.035l-1.21 1.21a11.042 11.042 0 005.516 5.516l1.21-1.21a1 1 0 011.035-.25l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-gray-400 dark:text-gray-500">{{ $branch->secondary_contact }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
