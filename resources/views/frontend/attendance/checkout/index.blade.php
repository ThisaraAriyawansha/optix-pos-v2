@extends('layouts.frontend')

@use('App\Models\Attendance')
@use('App\Models\WorkEntry')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Check Out: :name', ['name' => $worker->name]),
        'crumbs' => [__('Attendance') => route('attendance.menu'), __('Check In / Check Out') => route('attendance'), __('Check Out') => null],
        'subtitle' => __('Enter what was made, loaded or dispatched today'),
    ])

    @php
        $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
        $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
        $card = 'rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-4';
        $paid = $entry?->isPaid();
        $currentMark = old('attendance', $entry && ! $paid ? $entry->attendance : $suggestedMark);
        if ($currentMark === 'absent') {
            $currentMark = $suggestedMark;
        }
        $lines = old('items', $entry?->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'work_activity_id' => $item->work_activity_id,
            'quantity' => (float) $item->quantity,
        ])->all() ?? []);
    @endphp

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <form action="{{ route('attendance.checkOut', $attendance) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                <div class="lg:col-span-2 space-y-4">

                    @if ($paid)
                        <div class="px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-amber-800 dark:text-amber-300 text-sm">
                            {{ __('Today\'s work is already paid in :code, so it cannot be changed. Only the check-out time will be saved.', ['code' => $entry->salaryPayment->payment_code]) }}
                        </div>
                    @endif

                    <fieldset class="{{ $card }}" @disabled($paid)>
                        <div>
                            <p class="{{ $label }}">{{ __('Attendance') }} <span class="text-accent">*</span></p>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['present', 'half_day'] as $value)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance" value="{{ $value }}" class="sr-only peer" onchange="recalc()" {{ $currentMark === $value ? 'checked' : '' }}>
                                        <span class="inline-block px-3.5 py-2 rounded-full text-sm font-medium border border-gray-300 dark:border-[#2a4a70] text-gray-600 dark:text-gray-300 bg-surface transition-colors
                                                     peer-checked:bg-[#004080] peer-checked:border-[#004080] peer-checked:text-white">
                                            {{ __(WorkEntry::ATTENDANCE[$value]) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                                {{ __('Suggested from hours worked: :mark', ['mark' => __(WorkEntry::ATTENDANCE[$suggestedMark])]) }}
                            </p>
                        </div>
                    </fieldset>

                    <fieldset class="{{ $card }}" @disabled($paid)>
                        <div>
                            <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Work Done Today') }}</h2>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('How many of each product were made, sorted, loaded or dispatched') }}</p>
                        </div>

                        @if ($products->isEmpty())
                            <div class="px-4 py-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No products yet. An admin needs to add products first.') }}
                            </div>
                        @else
                            <div id="work-lines" class="space-y-2"></div>
                            <button type="button" onclick="addLine()"
                                    class="w-full py-2.5 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#2a4a70] text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-brand hover:border-gray-400 transition-colors">
                                + {{ __('Add work') }}
                            </button>
                        @endif
                    </fieldset>
                </div>

                {{-- ── shift ── --}}
                <aside class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-3">
                    <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Today\'s Shift') }}</h2>
                    <dl class="space-y-2 text-sm font-sans">
                        <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Checked in') }}</dt><dd>{{ $attendance->check_in_at->format('h:i A') }}</dd></div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Checking out') }}</dt><dd>{{ now()->format('h:i A') }}</dd></div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Pay type') }}</dt><dd>{{ $worker->payTypeLabel() }}</dd></div>
                    </dl>
                    <div class="rounded-xl px-4 py-3 bg-surface-alt border border-subtle">
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Hours worked') }}</p>
                        <p class="font-heading font-semibold text-2xl text-gray-900 dark:text-white">{{ Attendance::formatMinutes($attendance->minutes()) }}</p>
                    </div>
                    <div class="rounded-xl px-4 py-3 bg-brand text-white">
                        <p class="text-xs text-white/70">{{ __('Earned today') }}</p>
                        <p class="font-heading font-semibold text-2xl" id="sum-total">Rs. 0.00</p>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold bg-red-600 text-white active:scale-95 transition-transform">
                        {{ __('Check Out & Save Work') }}
                    </button>
                    <a href="{{ route('attendance') }}" class="block text-center w-full py-2.5 rounded-xl text-sm font-semibold border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200">
                        {{ __('Cancel') }}
                    </a>
                </aside>
            </div>
        </form>
    </main>

    <template id="work-line-template">
        <div class="work-line grid grid-cols-12 gap-2 items-center p-2 rounded-xl bg-surface-alt border border-subtle">
            <select data-name="product_id" class="{{ $field }} col-span-12 sm:col-span-5" onchange="recalc()" required>
                <option value="" disabled selected>{{ __('Product') }}</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            <select data-name="work_activity_id" class="{{ $field }} col-span-7 sm:col-span-4" onchange="recalc()" required>
                <option value="" disabled selected>{{ __('Work') }}</option>
                @foreach ($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->label() }}</option>
                @endforeach
            </select>
            <input type="number" data-name="quantity" step="0.01" min="0.01" placeholder="{{ __('Qty') }}" required inputmode="decimal"
                   class="{{ $field }} col-span-3 sm:col-span-2 text-right" oninput="recalc()">
            <button type="button" onclick="this.closest('.work-line').remove(); renumber(); recalc();" title="{{ __('Remove') }}"
                    class="col-span-2 sm:col-span-1 w-9 h-9 justify-self-end rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>

    <script>
        const RATES = @json($rateMap);
        const PAY_TYPE = @json($worker->pay_type);
        const DAILY_RATE = {{ (float) $worker->daily_rate }};
        const money = (value) => 'Rs. ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function addLine(line = {}) {
            const container = document.getElementById('work-lines');
            if (!container) return;
            const node = document.getElementById('work-line-template').content.firstElementChild.cloneNode(true);
            ['product_id', 'work_activity_id', 'quantity'].forEach((key) => {
                if (line[key]) node.querySelector(`[data-name=${key}]`).value = line[key];
            });
            container.appendChild(node);
            renumber();
            recalc();
        }

        function renumber() {
            document.querySelectorAll('#work-lines .work-line').forEach((row, index) => {
                row.querySelectorAll('[data-name]').forEach((input) => input.name = `items[${index}][${input.dataset.name}]`);
            });
        }

        function recalc() {
            let total = 0;
            if (PAY_TYPE === 'piece_rate') {
                document.querySelectorAll('#work-lines .work-line').forEach((row) => {
                    const product = row.querySelector('[data-name=product_id]').value;
                    const activity = row.querySelector('[data-name=work_activity_id]').value;
                    const qty = parseFloat(row.querySelector('[data-name=quantity]').value) || 0;
                    total += ((RATES[product] && RATES[product][activity]) || 0) * qty;
                });
            } else if (PAY_TYPE === 'daily') {
                const mark = document.querySelector('input[name=attendance]:checked')?.value;
                total = mark === 'present' ? DAILY_RATE : mark === 'half_day' ? DAILY_RATE / 2 : 0;
            }
            document.getElementById('sum-total').textContent = money(total);
        }

        const initialLines = @json(array_values($lines));
        initialLines.forEach((line) => addLine(line));
        recalc();
    </script>
@endsection
