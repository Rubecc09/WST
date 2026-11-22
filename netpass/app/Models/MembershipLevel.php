<?php

namespace App\Models;

use Database\Factories\MembershipLevelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'fee', 'validity_days', 'benefits'])]
class MembershipLevel extends Model
{
    /** @use HasFactory<MembershipLevelFactory> */
    use HasFactory;

    public function transactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    protected function casts(): array
    {
        return ['fee' => 'decimal:2', 'validity_days' => 'integer'];
    }

    public function isConfigured(): bool
    {
        return $this->fee !== null && $this->validity_days > 0 && filled($this->benefits);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
