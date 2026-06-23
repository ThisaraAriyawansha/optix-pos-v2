{{-- ────────────────────────── UPDATE BRANCH FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-2xl mx-auto">

    <form action="{{ route('branches.update', $branch) }}" method="POST" class="rounded-2xl bg-surface border border-subtle p-5 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Branch Name <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" required
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
                      class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">{{ old('description', $branch->description) }}</textarea>
            @error('description')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Address <span class="text-accent">*</span>
            </label>
            <input type="text" name="address" id="address" value="{{ old('address', $branch->address) }}" required
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
                <input type="text" name="main_contact" id="main_contact" value="{{ old('main_contact', $branch->main_contact) }}" required
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
                <input type="text" name="secondary_contact" id="secondary_contact" value="{{ old('secondary_contact', $branch->secondary_contact) }}"
                       placeholder="Optional"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('secondary_contact')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- status toggle --}}
        <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-surface-alt">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Branch Status</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">Inactive branches are hidden from active operations.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" {{ old('status', $branch->status) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-red-400 dark:bg-red-600 rounded-full peer-checked:bg-green-500 dark:peer-checked:bg-green-600 transition-colors"></div>
                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('branches') }}"
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
