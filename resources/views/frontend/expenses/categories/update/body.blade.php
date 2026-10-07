{{-- ────────────────────────── UPDATE EXPENSE TYPE FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-3xl mx-auto">

    @include('frontend.expenses.partials.alerts')

    <form action="{{ route('expenses.categories.update', $category) }}" method="POST"
          class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf
        @method('PUT')

        @include('frontend.expenses.categories.partials.fields', ['category' => $category])

        {{-- status toggle --}}
        <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-surface-alt border border-gray-300 dark:border-[#2a4a70]">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Type Status') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Inactive types cannot be picked for new expenses.') }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-red-400 dark:bg-red-600 rounded-full peer-checked:bg-green-500 dark:peer-checked:bg-green-600 transition-colors"></div>
                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('expenses.categories') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                {{ __('Cancel') }}
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>

    {{-- delete (only types with no expenses) --}}
    <form action="{{ route('expenses.categories.destroy', $category) }}" method="POST"
          data-confirm="{{ __('Delete :name?', ['name' => $category->label()]) }}"
          data-confirm-title="{{ __('Delete type') }}" data-confirm-ok="{{ __('Delete') }}" data-confirm-variant="danger"
          class="mt-4 flex flex-wrap items-center justify-between gap-3 px-5 py-4 rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-900/10">
        @csrf
        @method('DELETE')
        <div>
            <p class="text-sm font-medium text-red-700 dark:text-red-400">{{ __('Delete this type') }}</p>
            <p class="text-xs text-red-600/70 dark:text-red-400/70 font-sans">{{ __('Only possible when no expenses use it. Otherwise, set it inactive.') }}</p>
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-red-600 text-white active:scale-95 transition-transform">
            {{ __('Delete') }}
        </button>
    </form>
</main>
