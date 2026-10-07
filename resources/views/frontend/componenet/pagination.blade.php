{{-- ────────────────────────── PAGINATION (default links view) ────────────────────────── --}}
@if ($paginator->hasPages())
    @php
        $pageBtn = 'inline-flex items-center justify-center min-w-[2.25rem] h-9 px-2.5 rounded-lg text-sm font-medium font-sans transition-colors';
        $idleBtn = $pageBtn.' bg-surface border border-gray-300 dark:border-[#2a4a70] text-gray-700 dark:text-gray-200 hover:bg-surface-alt active:scale-95';
        $offBtn = $pageBtn.' bg-surface border border-gray-200 dark:border-[#1c3350] text-gray-300 dark:text-gray-600 cursor-not-allowed';
        $prevIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>';
        $nextIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';
    @endphp

    <nav role="navigation" aria-label="{{ __('Page navigation') }}" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-sans">
            {{ __('Showing :from–:to of :total', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="{{ $offBtn }}" aria-disabled="true" aria-label="{{ __('Previous') }}">{!! $prevIcon !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $idleBtn }}" aria-label="{{ __('Previous') }}">{!! $prevIcon !!}</a>
            @endif

            {{-- phone: compact "page x / y" --}}
            <span class="sm:hidden px-2 text-sm text-gray-600 dark:text-gray-300 font-sans">
                {{ __('Page :current / :last', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}
            </span>

            {{-- larger screens: page numbers --}}
            <div class="hidden sm:flex items-center gap-1.5">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-1 text-sm text-gray-400 dark:text-gray-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $pageBtn }} bg-[#004080] border border-[#004080] text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $idleBtn }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $idleBtn }}" aria-label="{{ __('Next') }}">{!! $nextIcon !!}</a>
            @else
                <span class="{{ $offBtn }}" aria-disabled="true" aria-label="{{ __('Next') }}">{!! $nextIcon !!}</span>
            @endif
        </div>
    </nav>
@endif
