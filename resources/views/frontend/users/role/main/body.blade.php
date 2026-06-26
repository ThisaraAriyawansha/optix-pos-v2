{{-- ────────────────────────── ROLES TABLE ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-sans">
            {{ session('success') }}
        </div>
    @endif

    @if ($roles->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
            <div class="w-12 h-12 rounded-full bg-surface-alt flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">No roles yet</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">Add your first role to get started.</p>
        </div>
    @else
        <div class="rounded-2xl bg-surface border border-subtle overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3 font-medium">Role Name</th>
                            <th class="px-4 py-3 font-medium">Users Assigned</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1c3350]">
                        @foreach ($roles as $role)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $role->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $role->users_count }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('roles.edit', $role) }}"
                                       class="text-brand text-xs font-medium hover:underline">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</main>
