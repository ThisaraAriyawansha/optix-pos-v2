@extends('layouts.frontend')

@use('App\Models\Expense')
@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Pay Salary'),
        'crumbs' => [__('Salary & Work') => route('salary'), __('Salary') => route('labour.salary'), __('Pay') => null],
        'subtitle' => __('Pay all unpaid work in a period — one day, a week or a month'),
        'actions' => [['url' => route('help').'#salary', 'label' => __('Help'), 'icon' => \App\Support\Help::ICON]],
    ])

    @php
        $field = 'w-full px-4 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004080]/40';
        $label = 'block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5';
        $workEarnings = (float) $entries->sum('total_earnings');
        $defaultBasic = $worker?->pay_type === 'monthly' ? (float) $worker->monthly_salary : 0;
    @endphp

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        {{-- ── step 1: worker & period ── --}}
        <form action="{{ route('labour.salary.create') }}" method="GET"
              class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div class="sm:col-span-2">
                <label for="worker" class="{{ $label }}">{{ __('Worker') }}</label>
                <select name="worker" id="worker" required class="{{ $field }}" onchange="this.form.from.value=''; this.form.submit()">
                    <option value="" disabled {{ $worker ? '' : 'selected' }}>{{ __('Select a worker') }}</option>
                    @foreach ($workers as $option)
                        <option value="{{ $option->id }}" {{ $worker?->id === $option->id ? 'selected' : '' }}>{{ $option->name }} · {{ $option->code }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="{{ $label }}">{{ __('From') }}</label>
                <input type="date" name="from" id="from" value="{{ $from }}" class="{{ $field }}" onchange="this.form.submit()">
            </div>
            <div>
                <label for="to" class="{{ $label }}">{{ __('To') }}</label>
                <input type="date" name="to" id="to" value="{{ $to }}" max="{{ now()->toDateString() }}" class="{{ $field }}" onchange="this.form.submit()">
            </div>
        </form>

        @if ($worker)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start mt-4">

                {{-- ── unpaid work ── --}}
                <div class="lg:col-span-2 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-[#24446a]">
                        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-base tracking-tight">{{ __('Unpaid Work') }}</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ $worker->payTypeLabel() }} · {{ trans_choice(':count day|:count days', $entries->count(), ['count' => $entries->count()]) }}</p>
                    </div>
                    @if ($entries->isEmpty())
                        <p class="px-5 py-10 text-center text-sm text-gray-400">{{ __('No unpaid work in this period.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm font-sans">
                                <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                                    @foreach ($entries as $entry)
                                        <tr>
                                            <td class="px-5 py-3 whitespace-nowrap align-top">
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $entry->work_date->translatedFormat('D, d M') }}</p>
                                                <p class="text-[11px] text-gray-400">{{ $entry->attendanceLabel() }}</p>
                                            </td>
                                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300 min-w-[14rem]">
                                                @foreach ($entry->items as $item)
                                                    <p>{{ Money::qty($item->quantity) }} {{ $item->product->name }} · {{ $item->activity->label() }}
                                                        @if ((float) $item->amount > 0) <span class="text-gray-400">({{ Money::format($item->amount) }})</span> @endif
                                                    </p>
                                                @endforeach
                                                @if ((float) $entry->daily_wage > 0)
                                                    <p>{{ __('Daily wage') }} <span class="text-gray-400">({{ Money::format($entry->daily_wage) }})</span></p>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap align-top">{{ Money::format($entry->total_earnings) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ── step 2: pay ── --}}
                <form action="{{ route('labour.salary.store') }}" method="POST"
                      data-confirm="{{ __('Pay this salary? The work will be marked as paid.') }}"
                      data-confirm-title="{{ __('Pay salary') }}" data-confirm-ok="{{ __('Pay') }}"
                      class="lg:sticky lg:top-4 rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="worker_id" value="{{ $worker->id }}">
                    <input type="hidden" name="period_from" value="{{ $from }}">
                    <input type="hidden" name="period_to" value="{{ $to }}">

                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                        <span>{{ __('Work earnings') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ Money::format($workEarnings) }}</span>
                    </div>

                    @foreach ([
                        'basic_salary' => [__('Basic salary'), old('basic_salary', $defaultBasic)],
                        'bonus' => [__('Bonus / allowance'), old('bonus', 0)],
                        'deductions' => [__('Deductions / advances'), old('deductions', 0)],
                    ] as $name => [$text, $value])
                        <div>
                            <label for="{{ $name }}" class="{{ $label }}">{{ $text }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">{{ $name === 'deductions' ? '− Rs.' : 'Rs.' }}</span>
                                <input type="number" name="{{ $name }}" id="{{ $name }}" step="0.01" min="0" inputmode="decimal" value="{{ $value }}"
                                       class="pay-input {{ $field }} {{ $name === 'deductions' ? 'pl-14' : 'pl-10' }}" oninput="recalcNet()">
                            </div>
                        </div>
                    @endforeach

                    @if ($worker->epf_enabled)
                        <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex justify-between">
                                <span>{{ __('EPF – employee (:rate%)', ['rate' => (float) $worker->epf_employee_rate]) }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">− <span id="epf-employee"></span></span>
                            </div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">
                                {{ __('Paid by business') }}: {{ __('EPF :rate%', ['rate' => (float) $worker->epf_employer_rate]) }} <span id="epf-employer"></span>
                                · {{ __('ETF :rate%', ['rate' => (float) $worker->etf_rate]) }} <span id="etf"></span>
                            </p>
                        </div>
                    @else
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ __('No EPF / ETF for this worker.') }}</p>
                    @endif

                    <div class="rounded-xl px-4 py-3 bg-brand text-white">
                        <p class="text-xs text-white/70">{{ __('Net pay') }}</p>
                        <p class="font-heading font-semibold text-2xl" id="net-pay">{{ Money::format($workEarnings + $defaultBasic) }}</p>
                    </div>

                    <div>
                        <p class="{{ $label }}">{{ __('Payment Method') }}</p>
                        <select name="payment_method" class="{{ $field }}">
                            @foreach (Expense::PAYMENT_METHODS as $value => $methodLabel)
                                <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>{{ __($methodLabel) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="paid_on" class="{{ $label }}">{{ __('Paid On') }}</label>
                        <input type="date" name="paid_on" id="paid_on" required max="{{ now()->toDateString() }}" value="{{ old('paid_on', now()->toDateString()) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="notes" class="{{ $label }}">{{ __('Notes') }}</label>
                        <input type="text" name="notes" id="notes" maxlength="1000" value="{{ old('notes') }}" placeholder="{{ __('Optional') }}" class="{{ $field }}">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">
                        {{ __('Pay & Issue Payslip') }}
                    </button>
                </form>
            </div>

            <script>
                function recalcNet() {
                    const val = (id) => parseFloat(document.getElementById(id).value) || 0;
                    const money = (amount) => 'Rs. ' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const pct = (rate) => Math.round(epfBase * rate) / 100;
                    // Same rule as the server: EPF / ETF on work earnings + basic, not bonus.
                    const epfBase = @json($worker->epf_enabled) ? Math.max({{ $workEarnings }} + val('basic_salary'), 0) : 0;
                    const epfEmployee = pct({{ (float) $worker->epf_employee_rate }});
                    const net = {{ $workEarnings }} + val('basic_salary') + val('bonus') - val('deductions') - epfEmployee;
                    document.getElementById('net-pay').textContent = money(net);

                    if (@json($worker->epf_enabled)) {
                        document.getElementById('epf-employee').textContent = money(epfEmployee);
                        document.getElementById('epf-employer').textContent = money(pct({{ (float) $worker->epf_employer_rate }}));
                        document.getElementById('etf').textContent = money(pct({{ (float) $worker->etf_rate }}));
                    }
                }
                recalcNet();
            </script>
        @endif
    </main>
@endsection
