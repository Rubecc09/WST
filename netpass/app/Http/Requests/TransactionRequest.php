<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() ?? false;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:members,id'],
            'membership_level_id' => ['required', 'integer', 'exists:membership_levels,id'],
            'remarks' => ['nullable', 'string', 'max:5000'],
            'status' => ['prohibited'],
            'reference_number' => ['prohibited'],
            'processed_by' => ['prohibited'],
            'membership_start_date' => ['prohibited'],
            'membership_end_date' => ['prohibited'],
        ];
    }
}
