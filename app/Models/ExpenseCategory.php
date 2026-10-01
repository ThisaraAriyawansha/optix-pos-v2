<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    public const COLORS = [
        'blue' => ['soft' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-300', 'solid' => 'bg-blue-500'],
        'green' => ['soft' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-300', 'solid' => 'bg-green-500'],
        'amber' => ['soft' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300', 'solid' => 'bg-amber-500'],
        'red' => ['soft' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300', 'solid' => 'bg-red-500'],
        'purple' => ['soft' => 'bg-purple-50 text-purple-600 dark:bg-purple-500/15 dark:text-purple-300', 'solid' => 'bg-purple-500'],
        'pink' => ['soft' => 'bg-pink-50 text-pink-600 dark:bg-pink-500/15 dark:text-pink-300', 'solid' => 'bg-pink-500'],
        'teal' => ['soft' => 'bg-teal-50 text-teal-600 dark:bg-teal-500/15 dark:text-teal-300', 'solid' => 'bg-teal-500'],
        'orange' => ['soft' => 'bg-orange-50 text-orange-600 dark:bg-orange-500/15 dark:text-orange-300', 'solid' => 'bg-orange-500'],
        'indigo' => ['soft' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300', 'solid' => 'bg-indigo-500'],
        'gray' => ['soft' => 'bg-gray-100 text-gray-600 dark:bg-gray-500/20 dark:text-gray-300', 'solid' => 'bg-gray-500'],
    ];

    public const ICONS = [
        'receipt' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6M9 16h3',
        'home' => 'M3 9.75L12 3l9 6.75V20a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5H10v5a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z',
        'bolt' => 'M13 10V3L4 14h7v7l9-11h-7z',
        'droplet' => 'M12 3s6 6.5 6 11a6 6 0 11-12 0c0-4.5 6-11 6-11z',
        'wifi' => 'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0',
        'phone' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 00-3-3.87',
        'truck' => 'M3 7h11v9H3zM14 10h4l3 3v3h-7M7.5 19a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM17.5 19a1.5 1.5 0 100-3 1.5 1.5 0 000 3z',
        'wrench' => 'M14.7 6.3a4 4 0 00-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.4-.6-.6-2.4 2.5-2.5z',
        'cart' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'megaphone' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
        'coffee' => 'M4 8h13v5a5 5 0 01-5 5H9a5 5 0 01-5-5V8zM17 9h1.5a2.5 2.5 0 010 5H17M6 3v2M10 3v2M14 3v2M4 21h14',
        'sparkles' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'dots' => 'M5 12h.01M12 12h.01M19 12h.01',
    ];

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function palette(): array
    {
        return self::COLORS[$this->color] ?? self::COLORS['blue'];
    }

    public function iconPath(): string
    {
        return self::ICONS[$this->icon] ?? self::ICONS['receipt'];
    }
}
