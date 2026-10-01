{{-- Expense type icon badge. Expects $category; optional $size (sm|md|lg). --}}
@php
    $badgeSize = ['sm' => 'w-8 h-8 rounded-lg', 'md' => 'w-10 h-10 rounded-xl', 'lg' => 'w-12 h-12 rounded-xl'][$size ?? 'md'];
    $iconSize = ['sm' => 'w-4 h-4', 'md' => 'w-5 h-5', 'lg' => 'w-6 h-6'][$size ?? 'md'];
@endphp
<span class="{{ $badgeSize }} {{ $category->palette()['soft'] }} shrink-0 inline-flex items-center justify-center">
    <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $category->iconPath() }}"/>
    </svg>
</span>
