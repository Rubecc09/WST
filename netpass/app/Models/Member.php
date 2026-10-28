<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use LogicException;

#[Fillable(['full_name', 'email', 'contact_number', 'address', 'membership_category_id', 'membership_level_id', 'registration_date', 'status'])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

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
