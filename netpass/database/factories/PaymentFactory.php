<?php

namespace Database\Factories;

use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'membership_transaction_id' => MembershipTransaction::factory(),
            'amount' => '100.00', 'method' => 'Cash', 'payment_date' => today(),
            'reference_number' => strtoupper((string) Str::ulid()),
            'idempotency_key' => (string) Str::uuid(),
            'recorded_by' => User::factory()->admin(), 'verified_at' => now(),
        ];
    }
}
