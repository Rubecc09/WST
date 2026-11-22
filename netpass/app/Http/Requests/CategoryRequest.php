<?php

namespace App\Http\Requests;

use App\Models\MembershipCategory;
use App\Rules\UniqueMembershipName;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:255', new UniqueMembershipName(MembershipCategory::class, $this->route('category')?->id)],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
