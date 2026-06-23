{{-- ────────────────────────── ADD BRANCH FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-2xl mx-auto">

    <form action="{{ route('branches.store') }}" method="POST" class="rounded-2xl bg-surface border border-subtle p-5 space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Branch Name <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   placeholder="e.g. Colombo Main Branch"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('name')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Description
            </label>
            <textarea name="description" id="description" rows="3"
                      placeholder="Short note about this branch (optional)"
                      class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Address <span class="text-accent">*</span>
            </label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" required
                   placeholder="e.g. No. 12, Galle Road, Colombo 03"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('address')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="main_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Main Contact Number <span class="text-accent">*</span>
                </label>
                <input type="text" name="main_contact" id="main_contact" value="{{ old('main_contact') }}" required
                       placeholder="e.g. 0771234567"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('main_contact')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="secondary_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Secondary Contact Number
                </label>
                <input type="text" name="secondary_contact" id="secondary_contact" value="{{ old('secondary_contact') }}"
                       placeholder="Optional"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('secondary_contact')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('branches') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                Cancel
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                Save Branch
            </button>
        </div>
    </form>
</main>
