<?php

namespace App\Models;

use Database\Factories\MembershipTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use LogicException;

#[Fillable(['member_id', 'membership_level_id', 'level_name', 'validity_days', 'transaction_type', 'amount', 'transaction_date', 'remarks', 'processed_by', 'latitude', 'longitude'])]
class MembershipTransaction extends Model
{
    /** @use HasFactory<MembershipTransactionFactory> */
    use HasFactory;

    public const APPLICATION = 'Application';

    public const RENEWAL = 'Renewal';

    public const PENDING = 'Pending';

    public const PAID = 'Paid';

    public const APPROVED = 'Approved';

    public const EXPIRED = 'Expired';

    public const CANCELLED = 'Cancelled';

    public const TYPES = [self::APPLICATION, self::RENEWAL];

    public const STATUSES = [self::PENDING, self::PAID, self::APPROVED, self::EXPIRED, self::CANCELLED];

    public const UNRESOLVED = [self::PENDING, self::PAID];

    protected $attributes = ['status' => self::PENDING];

    protected static function booted(): void
    {
        static::created(function (MembershipTransaction $transaction): void {
            $prefix = $transaction->transaction_type === self::APPLICATION ? 'APP' : 'REN';
            $transaction->reference_number = $prefix.'-'.$transaction->transaction_date->format('Y').'-'.str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT);
            $transaction->saveQuietly();
        });
        static::updating(function (MembershipTransaction $transaction): void {
            if ($transaction->isDirty()) {
                throw new LogicException('Transaction records are immutable. Use an authorized status action.');
            }
        });
        static::deleting(function (MembershipTransaction $transaction): void {
            throw new LogicException('Transaction history must be preserved.');
        });
    }

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'amount' => 'decimal:2', 'validity_days' => 'integer', 'transaction_date' => 'datetime',
            'membership_start_date' => 'date', 'membership_end_date' => 'date', 'approved_at' => 'datetime'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidCents(): int
    {
        return $this->payments()->whereNotNull('verified_at')->get()
            ->sum(fn (Payment $payment): int => Payment::cents($payment->amount));
    }

    public function balanceCents(): int
    {
        return Payment::cents($this->amount) - $this->paidCents();
    }

    public function canApprove(): bool
    {
        return ($this->status === self::PAID || ($this->status === self::PENDING && $this->amount === '0.00'))
            && $this->balanceCents() === 0;
    }

    public function canCancel(): bool
    {
        return $this->status === self::PENDING && ! $this->payments()->exists();
    }

    public function cancel(): bool
    {
        return DB::transaction(function (): bool {
            $transaction = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if (! $transaction->canCancel()) {
                return false;
            }
            self::whereKey($this->id)->update(['status' => self::CANCELLED]);
            $this->refresh();

            return true;
        }, 3);
    }
}
