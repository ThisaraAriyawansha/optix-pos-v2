<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkEntry extends Model
{
    public const ATTENDANCE = [
        'present' => 'Present',
        'half_day' => 'Half day',
        'absent' => 'Absent',
    ];

    protected $fillable = [
        'worker_id',
        'branch_id',
        'user_id',
        'salary_payment_id',
        'work_date',
        'attendance',
        'daily_wage',
        'piece_earnings',
        'total_earnings',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'daily_wage' => 'decimal:2',
        'piece_earnings' => 'decimal:2',
        'total_earnings' => 'decimal:2',
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

    public function salaryPayment(): BelongsTo
    {
        return $this->belongsTo(SalaryPayment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WorkEntryItem::class);
    }

    /**
     * Replace the work lines, price each one from the product's activity rate and roll
     * the totals up to the entry. Only piece-rate workers are paid per line; for others
     * the quantities still count toward production but earn nothing extra.
     *
     * @param  array<int, array{product_id: int, work_activity_id: int, quantity: float}>  $lines
     */
    public function syncItems(array $lines): void
    {
        $worker = $this->worker()->first();
        $products = Product::with('activityRates')->whereIn('id', array_column($lines, 'product_id'))->get()->keyBy('id');
        $activities = WorkActivity::whereIn('id', array_column($lines, 'work_activity_id'))->get()->keyBy('id');
        $paysPerPiece = $worker->pay_type === 'piece_rate';

        $this->items()->delete();

        foreach ($lines as $line) {
            $rate = $paysPerPiece ? $products[$line['product_id']]->rateFor($activities[$line['work_activity_id']]) : 0;

            $this->items()->create([
                'product_id' => $line['product_id'],
                'work_activity_id' => $line['work_activity_id'],
                'quantity' => $line['quantity'],
                'rate' => $rate,
                'amount' => round($rate * $line['quantity'], 2),
            ]);
        }

        $dailyWage = $worker->wageFor($this->attendance);
        $pieceEarnings = (float) $this->items()->sum('amount');

        $this->update([
            'daily_wage' => $dailyWage,
            'piece_earnings' => $pieceEarnings,
            'total_earnings' => $dailyWage + $pieceEarnings,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->salary_payment_id !== null;
    }

    public function attendanceLabel(): string
    {
        return __(self::ATTENDANCE[$this->attendance] ?? 'Present');
    }
}
