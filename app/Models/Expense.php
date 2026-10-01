<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'card' => 'Card',
        'bank_transfer' => 'Bank Transfer',
        'cheque' => 'Cheque',
        'online' => 'Online / Mobile',
        'other' => 'Other',
    ];

    protected $fillable = [
        'expense_code',
        'branch_id',
        'expense_category_id',
        'user_id',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'paid_to',
        'reference_no',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethodLabel(): string
    {
        return __(self::PAYMENT_METHODS[$this->payment_method] ?? 'Other');
    }

    public function receiptUrl(): ?string
    {
        return $this->receipt_path ? Storage::disk('public')->url($this->receipt_path) : null;
    }

    public function receiptIsImage(): bool
    {
        return $this->receipt_path && ! str_ends_with(strtolower($this->receipt_path), '.pdf');
    }

    public static function money($amount): string
    {
        return 'Rs. '.number_format((float) $amount, 2);
    }
}
