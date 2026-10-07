{{-- Employee ID for staff. Hidden for roles marked "not an employee" (owners), which carry no ID. Expects $value. --}}
<div id="employee-code-field">
    <label for="employee_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
        {{ __('Employee ID') }}
    </label>
    <input type="text" name="employee_code" id="employee_code" value="{{ $value }}" maxlength="20"
           class="w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans uppercase text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand">
    <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">
        {{ __('Shared with labourers. Leave as is to use the next free ID.') }}
    </p>
    @error('employee_code')
        <p class="text-xs text-accent mt-1">{{ $message }}</p>
    @enderror
</div>

<div id="owner-note" class="hidden px-4 py-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-sm font-sans">
    {{ __('This role is not an employee role, so no Employee ID is needed and they do not check in or out.') }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ownerRoles = @json(\App\Models\UserRole::where('is_employee', false)->pluck('id')->map(fn ($id) => (string) $id));
        const role = document.getElementById('role_id');
        const field = document.getElementById('employee-code-field');
        const input = document.getElementById('employee_code');
        const note = document.getElementById('owner-note');

        const sync = () => {
            const isOwner = ownerRoles.includes(role.value);
            field.classList.toggle('hidden', isOwner);
            note.classList.toggle('hidden', ! isOwner);
            input.disabled = isOwner;
        };

        role.addEventListener('change', sync);
        sync();
    });
</script>
