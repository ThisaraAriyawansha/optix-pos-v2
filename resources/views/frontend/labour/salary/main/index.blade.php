@extends('layouts.frontend')

@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Salary'),
        'crumbs' => [__('Labour') => route('labour'), __('Salary') => null],
        'subtitle' => __('Unpaid balances and payment history'),
        'actions' => [['url' => route('labour.salary.create'), 'label' => __('Pay Salary'), 'primary' => true]],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <div class="rounded-2xl bg-brand text-white p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-white/70 font-sans">{{ __('Total Unpaid') }}</p>
                <p class="font-heading font-semibold text-xl sm:text-2xl tracking-tight mt-1">{{ Money::format($stats['unpaid']) }}</p>
            </div>
            <div class="rounded-2xl bg-surface border border-subtle p-4 sm:p-5 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans">{{ __('Paid This Month') }}</p>
                <p class="font-heading font-semibold text-xl sm:text-2xl text-gray-900 dark:text-white tracking-tight mt-1">{{ Money::format($stats['paid_month']) }}</p>
            </div>
        </div>

        {{-- ── balances ── --}}
        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight mt-8">{{ __('Balances to Pay') }}</h2>
        <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

        <div class="rounded-2xl bg-surface border border-gray-300 dark:border-[#2a4a70] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-sans">
                    <thead>
                        <tr class="bg-surface-alt text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-300 dark:border-[#2a4a70]">
                            <th class="px-4 py-3 font-medium">{{ __('Worker') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Pay Type') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Unpaid Days') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Last Paid') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Balance') }}</th>
                            <th class="px-4 py-3 font-medium text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#24446a]">
                        @forelse ($workers as $worker)
                            <tr class="hover:bg-surface-alt transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $worker->name }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $worker->code }} · {{ $worker->branch?->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $worker->payTypeLabel() }}</td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ $worker->unpaid_days }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    {{ $worker->last_paid_on ? \Illuminate\Support\Carbon::parse($worker->last_paid_on)->translatedFormat('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold whitespace-nowrap {{ $worker->unpaid_total > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-gray-600' }}">
                                    {{ Money::format($worker->unpaid_total) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('labour.salary.create', ['worker' => $worker->id]) }}"
                                       class="inline-flex px-3 py-1.5 rounded-lg text-xs font-medium {{ $worker->unpaid_total > 0 || $worker->pay_type === 'monthly' ? 'bg-brand text-white' : 'bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200' }}">
                                        {{ __('Pay') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-14 text-center text-sm text-gray-400">{{ __('No active workers.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── history ── --}}
        <h2 class="font-heading font-semibold text-gray-900 dark:text-white text-lg tracking-tight mt-8">{{ __('Payment History') }}</h2>
        <div class="h-px bg-gray-200/80 dark:bg-[#1c3350] mt-3 mb-4"></div>

        @if ($payments->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-14 rounded-2xl bg-surface border border-subtle">
                <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No salary paid yet') }}</p>
            </div>
        @else
            <div class="rounded-2xl bg-surface border border-subtle shadow-sm divide-y divide-gray-100 dark:divide-[#1c3350] overflow-hidden">
                @foreach ($payments as $payment)
                    <a href="{{ route('labour.salary.show', $payment) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-alt transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $payment->worker->name }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans truncate">
                                {{ $payment->payment_code }} · {{ $payment->period_from->translatedFormat('d M') }} – {{ $payment->period_to->translatedFormat('d M Y') }} · {{ __('by :name', ['name' => $payment->user->name ?? __('Unknown')]) }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Money::format($payment->net_amount) }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">{{ $payment->paid_on->translatedFormat('d M Y') }} · {{ $payment->paymentMethodLabel() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            @if ($payments->hasPages())
                <div class="mt-4">{{ $payments->links() }}</div>
            @endif
        @endif
    </main>
@endsection
