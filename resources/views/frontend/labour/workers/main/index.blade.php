@extends('layouts.frontend')

@use('App\Models\Worker')
@use('App\Support\Money')

@section('content')
    @include('frontend.componenet.pagehero', [
        'title' => __('Workers'),
        'crumbs' => [__('Workers') => route('labour'), __('All Workers') => null],
        'subtitle' => trans_choice(':count worker|:count workers', $workers->count(), ['count' => $workers->count()]),
        'actions' => [['url' => route('labour.workers.create'), 'label' => __('Add Worker'), 'primary' => true]],
    ])

    <main class="px-5 pt-5 pb-28 max-w-6xl mx-auto">
        @include('frontend.componenet.alerts')

        @php $filterField = 'px-3 py-2.5 rounded-xl bg-surface border border-gray-300 dark:border-[#2a4a70] text-sm font-sans text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004080]/40'; @endphp
        <form action="{{ route('labour.workers') }}" method="GET" class="flex flex-wrap gap-2 mb-4">
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Name, code, NIC or phone') }}" class="{{ $filterField }} flex-1 min-w-[12rem]">
            <select name="branch_id" onchange="this.form.submit()" class="{{ $filterField }}">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (string) ($filters['branch_id'] ?? '') === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
            <select name="pay_type" onchange="this.form.submit()" class="{{ $filterField }}">
                <option value="">{{ __('All pay types') }}</option>
                @foreach (Worker::PAY_TYPES as $value => $payLabel)
                    <option value="{{ $value }}" {{ ($filters['pay_type'] ?? '') === $value ? 'selected' : '' }}>{{ __($payLabel) }}</option>
                @endforeach
            </select>
        </form>

        @if ($workers->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-20 rounded-2xl bg-surface border border-subtle">
                <p class="font-heading font-semibold text-gray-900 dark:text-white text-sm">{{ __('No workers found') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-sans mt-1">{{ __('Add labourers and staff who are paid wages or salary.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($workers as $worker)
                    <div class="flex flex-col rounded-2xl bg-surface border border-subtle shadow-sm p-4 {{ $worker->is_active ? '' : 'opacity-60' }}">
                        <div class="flex items-start gap-3">
                            <span class="w-10 h-10 shrink-0 rounded-full bg-surface-alt flex items-center justify-center font-heading font-semibold text-gray-700 dark:text-gray-200">
                                {{ mb_strtoupper(mb_substr($worker->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white tracking-tight truncate">{{ $worker->name }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans truncate">
                                    {{ collect([$worker->code, $worker->branch?->name, $worker->phone])->filter()->implode(' · ') }}
                                </p>
                            </div>
                            <form action="{{ route('labour.workers.toggleStatus', $worker) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium active:scale-90 transition-transform
                                    {{ $worker->is_active ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' }}">
                                    {{ $worker->is_active ? __('Active') : __('Inactive') }}
                                </button>
                            </form>
                        </div>

                        <div class="flex items-end justify-between mt-4 pt-3 border-t border-gray-100 dark:border-[#1c3350]">
                            <div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-sans">
                                    {{ $worker->payTypeLabel() }}
                                    @if ($worker->pay_type === 'daily') · {{ Money::format($worker->daily_rate) }} / {{ __('day') }} @endif
                                    @if ($worker->pay_type === 'monthly') · {{ Money::format($worker->monthly_salary) }} / {{ __('month') }} @endif
                                </p>
                                <p class="font-heading font-semibold {{ $worker->unpaid_total > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300 dark:text-gray-600' }}">
                                    {{ Money::format($worker->unpaid_total) }} <span class="text-xs font-sans font-normal text-gray-400">{{ __('unpaid') }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('labour.work.create', ['worker' => $worker->id]) }}" title="{{ __('Enter today\'s work') }}"
                                   class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </a>
                                <a href="{{ route('labour.salary.create', ['worker' => $worker->id]) }}" title="{{ __('Pay salary') }}"
                                   class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </a>
                                <a href="{{ route('labour.workers.edit', $worker) }}" title="{{ __('Edit') }}"
                                   class="btn-icon-brand inline-flex w-8 h-8 items-center justify-center rounded-lg bg-surface-alt border border-subtle text-gray-700 dark:text-gray-200 active:scale-90 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
@endsection
