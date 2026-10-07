{{-- ────────────────────────── ADD USER FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-[1600px] mx-auto">

    <form action="{{ route('users.store') }}" method="POST" class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf

        <div>
            <label for="employee_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Employee ID') }}
            </label>
            <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code', $nextCode) }}" maxlength="20"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans uppercase text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
                {{ __('Shared with labourers. Leave as is to use the next free ID.') }}
            </p>
            @error('employee_code')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Name') }} <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   placeholder="{{ __('e.g. Nimal Perera') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            @error('name')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Email') }} <span class="text-accent">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   placeholder="{{ __('e.g. nimal@optix.com') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            @error('email')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Phone Number') }} <span class="text-accent">*</span>
                </label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required
                       placeholder="{{ __('e.g. 0771234567') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                @error('phone_number')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Role') }} <span class="text-accent">*</span>
                </label>
                <select name="role_id" id="role_id" required
                        class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                    <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>{{ __('Select a role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
                @if ($roles->isEmpty())
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
                        {{ __('No roles yet —') }} <a href="{{ route('roles.create') }}" class="text-brand underline">{{ __('add one first') }}</a>.
                    </p>
                @endif
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Address') }} <span class="text-accent">*</span>
            </label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" required
                   placeholder="{{ __('e.g. No. 12, Galle Road, Colombo 03') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            @error('address')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Branch') }}
            </label>
            <select name="branch_id" id="branch_id"
                    class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                <option value="">{{ __('No branch (e.g. Admin)') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
                {{ __('Optional — leave unassigned for roles that aren\'t tied to a single branch.') }}
            </p>
            @error('branch_id')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Password') }} <span class="text-accent">*</span>
                </label>
                <input type="password" name="password" id="password" required minlength="8"
                       placeholder="{{ __('Min. 8 characters') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                @error('password')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Confirm Password') }} <span class="text-accent">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                       placeholder="{{ __('Re-enter password') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('users.manage') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                {{ __('Cancel') }}
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Save User') }}
            </button>
        </div>
    </form>
</main>
