<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->reference_number)) {
            $this->merge(['reference_number' => strtoupper(trim($this->reference_number))]);
        }
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/', 'numeric', 'gt:0'],
            'method' => ['required', Rule::in(Payment::METHODS)],
            'payment_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'reference_number' => ['required', 'string', 'max:100', 'regex:/^[A-Z0-9][A-Z0-9\-\/_\.]*$/'],
            'idempotency_key' => ['required', 'uuid'],
            'verified' => ['accepted'],
            'recorded_by' => ['prohibited'],
            'verified_at' => ['prohibited'],
            'status' => ['prohibited'],
        ];
    }
}
