@extends('layouts.frontend')

@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Payslip :code', ['code' => $payment->payment_code]),
        'crumbs' => [__('Labour') => route('labour'), __('Salary') => route('labour.salary'), $payment->payment_code => null],
    ])

    <main class="px-5 pt-5 pb-28 max-w-3xl mx-auto print:p-0 print:max-w-none">
        @include('frontend.componenet.alerts')

        <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] p-6 space-y-5 print:border-0 print:p-0">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('Payslip') }}</p>
                    <p class="font-heading font-semibold text-xl text-gray-900 dark:text-white">{{ $payment->worker->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ collect([$payment->worker->code, $payment->worker->nic, $payment->worker->branch?->name])->filter()->implode(' · ') }}</p>
                </div>
                <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $payment->payment_code }}</p>
                    <p>{{ __('Paid on :date', ['date' => $payment->paid_on->translatedFormat('d M Y')]) }}</p>
                    <p>{{ $payment->paymentMethodLabel() }}</p>
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                {{ __('Period') }}: <span class="font-medium">{{ $payment->period_from->translatedFormat('d M Y') }} – {{ $payment->period_to->translatedFormat('d M Y') }}</span>
            </p>

            @if ($payment->workEntries->isNotEmpty())
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                            <th class="py-2 font-medium">{{ __('Date') }}</th>
                            <th class="py-2 font-medium">{{ __('Work') }}</th>
                            <th class="py-2 font-medium text-right">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                        @foreach ($payment->workEntries as $entry)
                            <tr>
                                <td class="py-2 whitespace-nowrap align-top text-gray-900 dark:text-white">{{ $entry->work_date->translatedFormat('d M') }}</td>
                                <td class="py-2 text-gray-600 dark:text-gray-300">
                                    @foreach ($entry->items as $item)
                                        <p>{{ Money::qty($item->quantity) }} × {{ $item->product->name }} · {{ $item->activity->label() }}
                                            @if ((float) $item->amount > 0) <span class="text-gray-400">@ {{ Money::format($item->rate) }}</span> @endif
                                        </p>
                                    @endforeach
                                    @if ((float) $entry->daily_wage > 0)
                                        <p>{{ __('Daily wage') }} ({{ $entry->attendanceLabel() }})</p>
                                    @endif
                                </td>
                                <td class="py-2 text-right whitespace-nowrap align-top text-gray-900 dark:text-white">{{ Money::format($entry->total_earnings) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <dl class="ml-auto max-w-xs space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Work earnings') }}</dt><dd>{{ Money::format($payment->work_earnings) }}</dd></div>
                @if ((float) $payment->basic_salary > 0)
                    <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Basic salary') }}</dt><dd>{{ Money::format($payment->basic_salary) }}</dd></div>
                @endif
                @if ((float) $payment->bonus > 0)
                    <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Bonus / allowance') }}</dt><dd>{{ Money::format($payment->bonus) }}</dd></div>
                @endif
                @if ((float) $payment->deductions > 0)
                    <div class="flex justify-between text-gray-600 dark:text-gray-300"><dt>{{ __('Deductions') }}</dt><dd>− {{ Money::format($payment->deductions) }}</dd></div>
                @endif
                <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-[#2a4a70] font-heading font-semibold text-lg text-gray-900 dark:text-white"><dt>{{ __('Net Paid') }}</dt><dd>{{ Money::format($payment->net_amount) }}</dd></div>
            </dl>

            @if ($payment->notes)
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Notes') }}: {{ $payment->notes }}</p>
            @endif

            <div class="grid grid-cols-2 gap-8 pt-10 text-xs text-gray-500 dark:text-gray-400 text-center">
                <p class="border-t border-gray-300 dark:border-[#2a4a70] pt-2">{{ __('Paid by') }}: {{ $payment->user->name ?? '' }}</p>
                <p class="border-t border-gray-300 dark:border-[#2a4a70] pt-2">{{ __('Received by') }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4 print:hidden">
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-brand text-white active:scale-95 transition-transform">{{ __('Print Payslip') }}</button>
            <a href="{{ route('labour.salary') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200">{{ __('Back to Salary') }}</a>
            @can('manage-pricing')
                <form action="{{ route('labour.salary.destroy', $payment) }}" method="POST" class="ml-auto"
                      onsubmit="return confirm(@js(__('Cancel this payment? Its work will become unpaid again.')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:underline">{{ __('Cancel Payment') }}</button>
                </form>
            @endcan
        </div>
    </main>
@endsection
