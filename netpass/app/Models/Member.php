<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use LogicException;

#[Fillable(['full_name', 'email', 'contact_number', 'address', 'membership_category_id', 'membership_level_id', 'registration_date', 'status'])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    public function transactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    public function approvedMembership(): HasOne
    {
        return $this->hasOne(MembershipTransaction::class)->ofMany(
            ['id' => 'max'],
            fn ($query) => $query->whereIn('status', [MembershipTransaction::APPROVED, MembershipTransaction::EXPIRED])
        );
    }

    public function unresolvedApplication(): HasOne
    {
        return $this->hasOne(MembershipTransaction::class)->ofMany(
            ['id' => 'max'],
            fn ($query) => $query->whereIn('status', MembershipTransaction::UNRESOLVED)
        );
    }

    public function paidMembershipStatus(): string
    {
        $approved = $this->approvedMembership;
        if (! $approved) {
            return 'Pending';
        }
        if ($approved->status === MembershipTransaction::EXPIRED || $approved->membership_end_date?->lt(today())) {
            return 'Expired';
        }
        if (! $approved->membership_start_date || ! $approved->membership_end_date || $approved->membership_start_date->gt(today())) {
            return 'Pending';
        }

        return 'Active';
    }

    protected $attributes = ['status' => 'Active'];

    protected static function booted(): void
    {
        static::creating(function (Member $member): void {
            $member->membership_number = 'NP-'.Str::ulid();
        });
        static::updating(function (Member $member): void {
            if ($member->isDirty('membership_number')) {
                throw new LogicException('The membership number cannot be changed.');
            }
        });
        static::deleting(function (Member $member): void {
            throw new LogicException('Deactivate members instead of deleting them.');
        });
    }

    protected function casts(): array
    {
        return ['registration_date' => 'date'];
    }

    public function membershipCategory(): BelongsTo
    {
        return $this->belongsTo(MembershipCategory::class);
    }

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'Inactive']);
    }

    public function reactivate(): void
    {
        $this->update(['status' => 'Active']);
    }
}
