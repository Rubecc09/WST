<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['membership_transaction_id', 'amount', 'method', 'payment_date', 'reference_number', 'idempotency_key', 'recorded_by', 'verified_at'])]
class Payment extends Model
{
    use HasFactory;

    public const METHODS = ['Cash', 'GCash', 'Maya', 'Bank Transfer'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date', 'verified_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(function (Payment $payment): void {
            throw new LogicException('Payment records are immutable.');
        });
        static::deleting(function (Payment $payment): void {
            throw new LogicException('Payment history must be preserved.');
        });
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(MembershipTransaction::class, 'membership_transaction_id');
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function cents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
