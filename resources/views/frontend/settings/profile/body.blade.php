{{-- ────────────────────────── MANAGE PROFILE FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-[1600px] mx-auto">

    <form action="{{ route('settings.profile.update') }}" method="POST" class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Name') }} <span class="text-accent">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                   placeholder="{{ __('e.g. Nimal Perera') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            @error('name')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Email') }} <span class="text-accent">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       placeholder="{{ __('e.g. nimal@optix.com') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                @error('email')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Phone Number') }} <span class="text-accent">*</span>
                </label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                       placeholder="{{ __('e.g. 0771234567') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                @error('phone_number')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Address') }} <span class="text-accent">*</span>
            </label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" required
                   placeholder="{{ __('e.g. No. 12, Galle Road, Colombo 03') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            @error('address')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- change password --}}
        <div class="pt-2 border-t border-gray-200 dark:border-[#2a4a70]">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mt-4">{{ __('Change Password') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Leave blank to keep your current password.') }}</p>
        </div>

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                {{ __('Current Password') }}
            </label>
            <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                   placeholder="{{ __('Required to set a new password') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            @error('current_password')
                <p class="text-xs text-accent mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('New Password') }}
                </label>
                <input type="password" name="password" id="password" minlength="8" autocomplete="new-password"
                       placeholder="{{ __('Leave blank to keep current') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
                @error('password')
                    <p class="text-xs text-accent mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Confirm New Password') }}
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" minlength="8" autocomplete="new-password"
                       placeholder="{{ __('Re-enter new password') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('settings') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                {{ __('Cancel') }}
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</main>
