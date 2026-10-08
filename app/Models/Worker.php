<?php

namespace App\Models;

use App\Support\EmployeeCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Worker extends Model
{
    public const PAY_TYPES = [
        'piece_rate' => 'Per work done',
        'daily' => 'Daily wage',
        'monthly' => 'Monthly salary',
    ];

    protected $fillable = [
        'code',
        'name',
        'nic',
        'phone',
        'address',
        'branch_id',
        'pay_type',
        'daily_rate',
        'monthly_salary',
        'epf_enabled',
        'epf_number',
        'epf_employee_rate',
        'epf_employer_rate',
        'etf_rate',
        'joined_on',
        'notes',
        'is_active',
        'track_attendance',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'epf_enabled' => 'boolean',
        'epf_employee_rate' => 'decimal:2',
        'epf_employer_rate' => 'decimal:2',
        'etf_rate' => 'decimal:2',
        'joined_on' => 'date',
        'is_active' => 'boolean',
        'track_attendance' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Worker $worker) {
            $worker->code = EmployeeCode::normalize($worker->code) ?? EmployeeCode::next();
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function workEntries(): HasMany
    {
        return $this->hasMany(WorkEntry::class);
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    /** Active labourers who check in and out. */
    public function scopeAttendanceTracked(Builder $query): void
    {
        $query->where('is_active', true)->where('track_attendance', true);
    }

    public function payTypeLabel(): string
    {
        return __(self::PAY_TYPES[$this->pay_type] ?? 'Per work done');
    }

    /**
     * EPF / ETF on the given earnings. The employee's EPF share comes off their pay;
     * the employer's EPF and ETF are paid on top by the business. All zero when not covered.
     *
     * @return array{epf_base: float, epf_employee: float, epf_employer: float, etf: float}
     */
    public function contributionsFor(float $earnings): array
    {
        $base = $this->epf_enabled ? round(max($earnings, 0), 2) : 0.0;

        return [
            'epf_base' => $base,
            'epf_employee' => round($base * (float) $this->epf_employee_rate / 100, 2),
            'epf_employer' => round($base * (float) $this->epf_employer_rate / 100, 2),
            'etf' => round($base * (float) $this->etf_rate / 100, 2),
        ];
    }

    /**
     * Daily wage owed for an attendance mark. Only daily-wage workers earn per day;
     * piece-rate workers earn from their work lines and monthly staff are paid on payday.
     */
    public function wageFor(string $attendance): float
    {
        if ($this->pay_type !== 'daily') {
            return 0;
        }

        return match ($attendance) {
            'present' => (float) $this->daily_rate,
            'half_day' => round((float) $this->daily_rate / 2, 2),
            default => 0,
        };
    }
}
