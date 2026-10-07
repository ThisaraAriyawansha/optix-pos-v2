<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryPayment extends Model
{
    protected $fillable = [
        'payment_code',
        'worker_id',
        'branch_id',
        'user_id',
        'period_from',
        'period_to',
        'work_earnings',
        'basic_salary',
        'bonus',
        'deductions',
        'net_amount',
        'payment_method',
        'paid_on',
        'notes',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'paid_on' => 'date',
        'work_earnings' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workEntries(): HasMany
    {
        return $this->hasMany(WorkEntry::class);
    }

    public function paymentMethodLabel(): string
    {
        return __(Expense::PAYMENT_METHODS[$this->payment_method] ?? 'Other');
    }
}
