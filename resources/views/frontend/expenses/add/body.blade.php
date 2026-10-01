{{-- ────────────────────────── ADD EXPENSE FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-4xl mx-auto">

    @include('frontend.expenses.partials.alerts')

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data"
          class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf

        @include('frontend.expenses.partials.form')

        {{-- primary button first in the DOM so Enter saves; row-reverse keeps it on the right --}}
        <div class="flex flex-col sm:flex-row-reverse gap-3 pt-2">
            <button type="submit" name="action" value="save"
                    class="sm:flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Save Expense') }}
            </button>
            <button type="submit" name="action" value="add_another"
                    class="sm:flex-1 py-2.5 rounded-xl text-sm font-medium bg-surface border-2 border-[#004080] dark:border-blue-400 text-[#004080] dark:text-blue-300 active:scale-95 transition-transform">
                {{ __('Save & Add Another') }}
            </button>
            <a href="{{ route('expenses') }}"
               class="sm:flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</main>
