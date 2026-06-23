{{-- ────────────────────────── EDIT USER FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-2xl mx-auto">

    <form action="{{ route('users.update', $user) }}" method="POST" class="rounded-2xl bg-surface border border-subtle p-5 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Name <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                   placeholder="e.g. Nimal Perera"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('name')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Email <span class="text-accent">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                   placeholder="e.g. nimal@optix.com"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('email')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Phone Number <span class="text-accent">*</span>
                </label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                       placeholder="e.g. 0771234567"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('phone_number')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Role <span class="text-accent">*</span>
                </label>
                <select name="role_id" id="role_id" required
                        class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                    <option value="" disabled>Select a role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
                @if ($roles->isEmpty())
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
                        No roles yet — <a href="{{ route('roles.create') }}" class="text-brand underline">add one first</a>.
                    </p>
                @endif
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Address <span class="text-accent">*</span>
            </label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" required
                   placeholder="e.g. No. 12, Galle Road, Colombo 03"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            @error('address')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                Branch
            </label>
            <select name="branch_id" id="branch_id"
                    class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                <option value="">No branch (e.g. Admin)</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
                Optional — leave unassigned for roles that aren't tied to a single branch.
            </p>
            @error('branch_id')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- status toggle --}}
        <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-surface-alt">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">User Status</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">Inactive users cannot log in.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="status" value="1" class="sr-only peer" {{ old('status', $user->status) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-red-400 dark:bg-red-600 rounded-full peer-checked:bg-green-500 dark:peer-checked:bg-green-600 transition-colors"></div>
                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    New Password
                </label>
                <input type="password" name="password" id="password" minlength="8"
                       placeholder="Leave blank to keep current"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
                @error('password')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    Confirm New Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                       placeholder="Re-enter new password"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface-alt border border-subtle text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('users.manage') }}"
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
