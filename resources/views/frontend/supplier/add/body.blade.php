{{-- ────────────────────────── ADD SUPPLIER FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-2xl mx-auto">

    <form action="{{ route('suppliers.store') }}" method="POST" class="rounded-2xl bg-surface border border-subtle p-5 space-y-5">
        @csrf

        <div>
            <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Branch <span class="text-accent">*</span>
            </label>
            <select name="branch_id" id="branch_id" required
                    class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                <option value="" disabled {{ old('branch_id') ? '' : 'selected' }}>Select a branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            @error('branch_id')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Supplier Name <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   placeholder="e.g. ABC Trading Co."
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('name')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       placeholder="Optional"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('email')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Phone <span class="text-accent">*</span>
                </label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                       placeholder="e.g. 0771234567"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('phone')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Address
            </label>
            <input type="text" name="address" id="address" value="{{ old('address') }}"
                   placeholder="e.g. No. 12, Galle Road, Colombo 03"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('address')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nic" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                NIC <span class="text-accent">*</span>
            </label>
            <input type="text" name="nic" id="nic" value="{{ old('nic') }}" required
                   placeholder="e.g. 991234567V"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('nic')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('suppliers') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                Cancel
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                Save Supplier
            </button>
        </div>
    </form>
</main>
