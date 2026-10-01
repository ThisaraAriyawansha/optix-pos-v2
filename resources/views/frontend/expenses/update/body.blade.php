{{-- ────────────────────────── UPDATE EXPENSE FORM ────────────────────────── --}}
<main class="px-5 pt-5 pb-28 max-w-4xl mx-auto">

    @include('frontend.expenses.partials.alerts')

    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data"
          class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-5">
        @csrf
        @method('PUT')

        @include('frontend.expenses.partials.form', ['expense' => $expense])

        <div class="flex gap-3 pt-2">
            <a href="{{ route('expenses.list') }}"
               class="flex-1 text-center py-2.5 rounded-xl text-sm font-medium bg-surface-alt border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                {{ __('Cancel') }}
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>

    {{-- delete --}}
    <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
          onsubmit="return confirm(this.dataset.confirm)" data-confirm="{{ __('Delete expense :code? This cannot be undone.', ['code' => $expense->expense_code]) }}"
          class="mt-4 flex flex-wrap items-center justify-between gap-3 px-5 py-4 rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-900/10">
        @csrf
        @method('DELETE')
        <div>
            <p class="text-sm font-medium text-red-700 dark:text-red-400">{{ __('Delete this expense') }}</p>
            <p class="text-xs text-red-600/70 dark:text-red-400/70 font-sans">{{ __('The record and its receipt will be removed permanently.') }}</p>
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-red-600 text-white active:scale-95 transition-transform">
            {{ __('Delete') }}
        </button>
    </form>
</main>
