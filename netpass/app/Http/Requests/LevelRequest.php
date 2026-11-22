<?php

namespace App\Http\Requests;

use App\Models\MembershipLevel;
use App\Rules\UniqueMembershipName;
use Illuminate\Foundation\Http\FormRequest;

class LevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:255', new UniqueMembershipName(MembershipLevel::class, $this->route('level')?->id)],
            'description' => ['nullable', 'string', 'max:5000'],
            'fee' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'validity_days' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'benefits' => ['required', 'string', 'max:10000'],
        ];
    }
}
