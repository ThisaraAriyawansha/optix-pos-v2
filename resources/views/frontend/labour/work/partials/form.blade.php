{{-- Daily work entry form. Expects $workers, $branches, $products, $activities, $rateMap, $date, $selectedWorker; optional $entry. --}}
@php
    $entry = $entry ?? null;
    $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
    $card = 'rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-4';

    $currentWorker = old('worker_id', $selectedWorker);
    $currentBranch = old('branch_id', $entry?->branch_id ?? $workers->firstWhere('id', $currentWorker)?->branch_id ?? auth()->user()?->branch_id);
    $currentAttendance = old('attendance', $entry?->attendance ?? 'present');
    $lines = old('items', $entry?->items->map(fn ($item) => [
        'product_id' => $item->product_id,
        'work_activity_id' => $item->work_activity_id,
        'quantity' => (float) $item->quantity,
    ])->all() ?? []);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
    <div class="lg:col-span-2 space-y-4">

        {{-- ── who & when ── --}}
        <div class="{{ $card }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="worker_id" class="{{ $label }}">{{ __('Worker') }} <span class="text-accent">*</span></label>
                    <select name="worker_id" id="worker_id" required class="{{ $field }}" onchange="workerChanged()">
                        <option value="" disabled {{ $currentWorker ? '' : 'selected' }}>{{ __('Select a worker') }}</option>
                        @foreach ($workers as $worker)
                            <option value="{{ $worker->id }}" {{ (string) $currentWorker === (string) $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }} · {{ $worker->code }}
                            </option>
                        @endforeach
                    </select>
                    <p id="worker-pay" class="text-[11px] text-gray-400 dark:text-gray-500 mt-1"></p>
                </div>
                <div>
                    <label for="work_date" class="{{ $label }}">{{ __('Date') }} <span class="text-accent">*</span></label>
                    <input type="date" name="work_date" id="work_date" required max="{{ now()->toDateString() }}"
                           value="{{ old('work_date', $date) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <p class="{{ $label }}">{{ __('Attendance') }} <span class="text-accent">*</span></p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (\App\Models\WorkEntry::ATTENDANCE as $value => $attendanceLabel)
                            <label class="cursor-pointer">
                                <input type="radio" name="attendance" value="{{ $value }}" class="sr-only peer" onchange="recalc()"
                                       {{ $currentAttendance === $value ? 'checked' : '' }}>
                                <span class="inline-block px-3.5 py-2 rounded-full text-sm font-medium border border-gray-300 dark:border-[#2a4a70] text-gray-600 dark:text-gray-300 bg-surface transition-colors
                                             peer-checked:bg-[#004080] peer-checked:border-[#004080] peer-checked:text-white">
                                    {{ __($attendanceLabel) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label for="branch_id" class="{{ $label }}">{{ __('Branch') }} <span class="text-accent">*</span></label>
                    <select name="branch_id" id="branch_id" required class="{{ $field }}">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string) $currentBranch === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── work done ── --}}
        <div class="{{ $card }}">
            <div>
                <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Work Done') }}</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('What the worker made, sorted, transported or loaded today') }}</p>
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
        </div>

        <div class="{{ $card }}">
            <label for="notes" class="{{ $label }}">{{ __('Notes') }}</label>
            <textarea name="notes" id="notes" rows="2" maxlength="1000" class="{{ $field }} resize-none"
                      placeholder="{{ __('Optional') }}">{{ old('notes', $entry?->notes) }}</textarea>
        </div>
    </div>

    {{-- ── earnings ── --}}
    <aside class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-3">
        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Earnings for the Day') }}</h2>
        <dl class="space-y-2 text-sm font-sans">
            <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Daily wage') }}</dt><dd id="sum-wage">Rs. 0.00</dd></div>
            <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Work done') }}</dt><dd id="sum-piece">Rs. 0.00</dd></div>
        </dl>
        <div class="rounded-xl px-4 py-3 bg-brand text-white">
            <p class="text-xs text-white/70">{{ __('Total earned') }}</p>
            <p class="font-heading font-semibold text-2xl" id="sum-total">Rs. 0.00</p>
        </div>
        <p id="pay-note" class="text-[11px] text-gray-400 dark:text-gray-500 font-sans"></p>

        <button type="submit" name="action" value="save" class="w-full py-3 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
            {{ $entry ? __('Update Work') : __('Save Work') }}
        </button>
        @unless ($entry)
            <button type="submit" name="action" value="add_another" class="w-full py-2.5 rounded-xl text-sm font-semibold border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 active:scale-95 transition-transform">
                {{ __('Save & Next Worker') }}
            </button>
        @endunless
    </aside>
</div>

<template id="work-line-template">
    <div class="work-line grid grid-cols-12 gap-2 items-center p-2 rounded-xl bg-surface-alt border border-subtle">
        <select data-name="product_id" class="{{ $field }} col-span-12 sm:col-span-4" onchange="recalc()" required>
            <option value="" disabled selected>{{ __('Product') }}</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
        <select data-name="work_activity_id" class="{{ $field }} col-span-6 sm:col-span-3" onchange="recalc()" required>
            <option value="" disabled selected>{{ __('Work') }}</option>
            @foreach ($activities as $activity)
                <option value="{{ $activity->id }}">{{ $activity->label() }}</option>
            @endforeach
        </select>
        <input type="number" data-name="quantity" step="0.01" min="0.01" placeholder="{{ __('Qty') }}" required inputmode="decimal"
               class="{{ $field }} col-span-4 sm:col-span-2 text-right" oninput="recalc()">
        <span data-role="amount" class="col-span-10 sm:col-span-2 text-right text-sm font-medium text-gray-900 dark:text-white leading-tight">
            Rs. 0.00
        </span>
        <button type="button" onclick="this.closest('.work-line').remove(); renumber(); recalc();" title="{{ __('Remove') }}"
                class="col-span-2 sm:col-span-1 w-9 h-9 justify-self-end rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</template>

@php
    $workerInfo = $workers->mapWithKeys(fn ($worker) => [$worker->id => [
        'pay_type' => $worker->pay_type,
        'pay_label' => $worker->payTypeLabel(),
        'daily_rate' => (float) $worker->daily_rate,
        'branch_id' => $worker->branch_id,
    ]]);
@endphp
<script>
    const RATES = @json($rateMap);
    const WORKERS = @json($workerInfo);
    const money = (value) => 'Rs. ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const num = (value) => parseFloat(value) || 0;

    function currentWorker() {
        return WORKERS[document.getElementById('worker_id').value] || null;
    }

    function workerChanged() {
        const worker = currentWorker();
        if (worker && worker.branch_id) document.getElementById('branch_id').value = worker.branch_id;
        recalc();
    }

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
        const worker = currentWorker();
        const paysPerPiece = worker && worker.pay_type === 'piece_rate';
        let piece = 0;

        document.querySelectorAll('#work-lines .work-line').forEach((row) => {
            const product = row.querySelector('[data-name=product_id]').value;
            const activity = row.querySelector('[data-name=work_activity_id]').value;
            const qty = num(row.querySelector('[data-name=quantity]').value);
            const rate = (RATES[product] && RATES[product][activity]) || 0;
            const amount = paysPerPiece ? rate * qty : 0;
            piece += amount;
            row.querySelector('[data-role=amount]').innerHTML = paysPerPiece && product && activity
                ? `${money(amount)}<span class="block text-[11px] font-normal text-gray-400">${qty} × ${money(rate)}</span>`
                : '<span class="text-gray-400">—</span>';
        });

        const attendance = document.querySelector('input[name=attendance]:checked')?.value;
        let wage = 0;
        if (worker && worker.pay_type === 'daily') {
            wage = attendance === 'present' ? worker.daily_rate : attendance === 'half_day' ? worker.daily_rate / 2 : 0;
        }

        document.getElementById('sum-wage').textContent = money(wage);
        document.getElementById('sum-piece').textContent = money(piece);
        document.getElementById('sum-total').textContent = money(wage + piece);
        document.getElementById('worker-pay').textContent = worker ? worker.pay_label + (worker.pay_type === 'daily' ? ' · ' + money(worker.daily_rate) + ' / {{ __('day') }}' : '') : '';
        document.getElementById('pay-note').textContent = !worker ? '' : {
            piece_rate: @json(__('Paid per unit of work using each product\'s rate.')),
            daily: @json(__('Paid the daily wage. Quantities are still counted for production.')),
            monthly: @json(__('Monthly staff are paid on payday. Quantities are still counted for production.')),
        }[worker.pay_type];
    }

    const initialLines = @json(array_values($lines));
    initialLines.length ? initialLines.forEach((line) => addLine(line)) : addLine();
    recalc();
</script>
