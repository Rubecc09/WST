<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class UniqueMembershipName implements ValidationRule
{
    /** @param class-string<Model> $model */
    public function __construct(private string $model, private ?int $ignoreId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = $this->model::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
            ->when($this->ignoreId !== null, fn ($query) => $query->where('id', '!=', $this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('This name is already in use.');
        }
    }
}
