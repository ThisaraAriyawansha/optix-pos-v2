{{-- Big menu tile, same look as the Home and Users menus. Expects $url, $label, $icon (svg path); optional $hint, $primary, $badge. --}}
<a href="{{ $url }}"
   class="group relative flex flex-col items-center justify-center gap-3 h-36 sm:h-40 lg:h-44 rounded-2xl shadow-sm hover:shadow-md active:scale-[0.97] transition-all duration-200 text-center px-3
          {{ ($primary ?? false) ? 'bg-brand text-white' : 'bg-surface border border-subtle' }}">
    @if (isset($badge) && $badge !== null && $badge !== '')
        <span class="absolute top-3 right-3 min-w-[1.75rem] h-7 px-2 rounded-full flex items-center justify-center text-xs font-semibold
                     {{ ($primary ?? false) ? 'bg-white text-brand' : 'bg-green-600 text-white' }}">{{ $badge }}</span>
    @endif
    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center {{ ($primary ?? false) ? 'bg-white/10' : 'bg-surface-alt' }}">
        <svg class="w-6 h-6 sm:w-7 sm:h-7 {{ ($primary ?? false) ? '' : 'text-gray-700 dark:text-gray-300' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
        </svg>
    </div>
    <span class="flex flex-col items-center gap-0.5">
        <span class="font-medium text-[15px] sm:text-base tracking-tight {{ ($primary ?? false) ? '' : 'text-gray-900 dark:text-white' }}">{{ $label }}</span>
        @if (! empty($hint))
            <span class="text-xs {{ ($primary ?? false) ? 'text-white/70' : 'text-gray-400 dark:text-gray-500' }}">{{ $hint }}</span>
        @endif
    </span>
</a>
