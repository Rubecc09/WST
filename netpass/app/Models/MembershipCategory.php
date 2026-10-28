<?php

namespace App\Models;

use Database\Factories\MembershipCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class MembershipCategory extends Model
{
    /** @use HasFactory<MembershipCategoryFactory> */
    use HasFactory;

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
