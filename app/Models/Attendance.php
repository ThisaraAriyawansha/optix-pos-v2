<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    public const METHODS = [
        'manual' => 'Manual',
        'fingerprint' => 'Fingerprint',
    ];

    protected $fillable = [
        'attendable_type',
        'attendable_id',
        'branch_id',
        'work_date',
        'check_in_at',
        'check_out_at',
        'check_in_method',
        'check_out_method',
        'check_in_by',
        'check_out_by',
        'device_sn',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    /** The labourer (Worker) or staff member (User). */
    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'check_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function scopeOpen(Builder $query): void
    {
        $query->whereNull('check_out_at');
    }

    public function isOpen(): bool
    {
        return $this->check_out_at === null;
    }

    public function isWorker(): bool
    {
        return $this->attendable_type === 'worker';
    }

    /** Still open from an earlier day: somebody forgot to check out. */
    public function missedCheckOut(): bool
    {
        return $this->isOpen() && $this->work_date->lt(today());
    }

    /** Minutes worked so far (up to now while still checked in). */
    public function minutes(?Carbon $until = null): int
    {
        $end = $this->check_out_at ?? ($this->missedCheckOut() ? $this->check_in_at : ($until ?? now()));

        return max(0, (int) $this->check_in_at->diffInMinutes($end));
    }

    public static function formatMinutes(int $minutes): string
    {
        return sprintf('%dh %02dm', intdiv($minutes, 60), $minutes % 60);
    }

    public function methodLabel(?string $method): string
    {
        return __(self::METHODS[$method] ?? 'Manual');
    }
}
