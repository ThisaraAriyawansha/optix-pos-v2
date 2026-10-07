<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    public const UNITS = ['Cube', 'Ton', 'Load', 'Unit', 'Bag', 'Piece'];

    public const CODE_PREFIX = 'PRD-';

    protected $fillable = [
        'code',
        'name',
        'unit',
        'description',
        'commission_type',
        'commission_value',
        'selling_price',
        'is_active',
    ];

    protected $casts = [
        'material_cost' => 'decimal:2',
        'labour_cost' => 'decimal:2',
        'handling_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->code = self::normalizeCode($product->code) ?? self::nextCode();
        });
    }

    /** Next free PRD- code, one past the highest number in use. */
    public static function nextCode(): string
    {
        $last = self::where('code', 'like', self::CODE_PREFIX.'%')->pluck('code')
            ->map(fn ($code) => (int) substr($code, strlen(self::CODE_PREFIX)))
            ->max() ?? 0;

        return self::CODE_PREFIX.str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }

    public static function normalizeCode(?string $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return $code === '' ? null : $code;
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'product_materials')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function activityRates(): BelongsToMany
    {
        return $this->belongsToMany(WorkActivity::class, 'product_activity_rates')
            ->withPivot('rate')
            ->withTimestamps();
    }

    /**
     * Rebuild the per-unit cost snapshot from the current material prices and
     * activity rates: material + labour + handling = total cost, then commission.
     */
    public function recalculateCosts(): void
    {
        $this->load(['materials', 'activityRates']);

        $this->material_cost = round($this->materials->sum(fn ($material) => $material->pivot->quantity * $material->unit_cost), 2);
        $this->labour_cost = round($this->activityRates->where('cost_group', 'labour')->sum('pivot.rate'), 2);
        $this->handling_cost = round($this->activityRates->where('cost_group', 'handling')->sum('pivot.rate'), 2);
        $this->total_cost = $this->material_cost + $this->labour_cost + $this->handling_cost;

        $this->commission_amount = $this->commission_type === 'percent'
            ? round($this->total_cost * $this->commission_value / 100, 2)
            : $this->commission_value;

        $this->save();
    }

    public function breakEvenPrice(): float
    {
        return (float) $this->total_cost + (float) $this->commission_amount;
    }

    public function profit(): float
    {
        return (float) $this->selling_price - $this->breakEvenPrice();
    }

    public function profitMargin(): ?float
    {
        return (float) $this->selling_price > 0
            ? round($this->profit() / (float) $this->selling_price * 100, 1)
            : null;
    }

    /**
     * Rate paid per unit of this product for an activity, falling back to the
     * activity's default rate when the product has no specific rate.
     */
    public function rateFor(WorkActivity $activity): float
    {
        $rate = $this->activityRates->firstWhere('id', $activity->id)?->pivot->rate;

        return (float) ($rate ?? $activity->default_rate);
    }
}
