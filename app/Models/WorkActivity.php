<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkActivity extends Model
{
    public const COST_GROUPS = [
        'labour' => 'Labour (making)',
        'handling' => 'Handling (sorting, transport, loading)',
    ];

    protected $fillable = [
        'name',
        'cost_group',
        'is_production',
        'is_dispatch',
        'default_rate',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_production' => 'boolean',
        'is_dispatch' => 'boolean',
        'default_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_activity_rates')
            ->withPivot('rate')
            ->withTimestamps();
    }

    /**
     * Display name in the current language. Seeded default activities have entries
     * in lang/*.json; user-created names fall through unchanged.
     */
    public function label(): string
    {
        return __($this->name);
    }

    public function costGroupLabel(): string
    {
        return __($this->cost_group === 'handling' ? 'Handling' : 'Labour');
    }
}
