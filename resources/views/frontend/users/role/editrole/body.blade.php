{{-- ────────────────────────── EDIT USER ROLE FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-2xl mx-auto">

    <form action="{{ route('roles.update', $role) }}" method="POST" class="rounded-2xl bg-surface border border-subtle p-5 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Role Name <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                   placeholder="e.g. Cashier, Manager, Admin"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('name')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('roles.manage') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                Cancel
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                Save Changes
            </button>
        </div>
    </form>
</main>
