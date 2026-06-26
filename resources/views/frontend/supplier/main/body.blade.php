{{-- ────────────────────────── SUPPLIER LIST BODY ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-sans">
            {{ session('success') }}
        </div>
    @endif

    @if ($suppliers->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
            <div class="w-12 h-12 rounded-full bg-surface-alt flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7l2-4h14l2 4M3 7v12a1 1 0 001 1h16a1 1 0 001-1V7M9 11h6"/>
                </svg>
            </div>
            <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">No suppliers yet</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">Add your first supplier to get started.</p>
        </div>
    @else
        <div class="rounded-2xl bg-surface border border-subtle overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Branch</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Address</th>
                            <th class="px-4 py-3 font-medium">NIC</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-right">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1c3350]">
                        @foreach ($suppliers as $supplier)
                            <tr onclick="window.location='{{ route('suppliers.edit', $supplier) }}'"
                                class="cursor-pointer hover:bg-surface-alt transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $supplier->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $supplier->branch->name ?? 'No branch' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $supplier->phone }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $supplier->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $supplier->address ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $supplier->nic }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <form action="{{ route('suppliers.toggleStatus', $supplier) }}" method="POST" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                            {{ $supplier->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" onclick="event.stopPropagation()"
                                       class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
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
