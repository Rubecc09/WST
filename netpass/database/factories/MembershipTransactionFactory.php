<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipTransaction> */
class MembershipTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'membership_level_id' => MembershipLevel::factory(),
            'level_name' => 'Test level',
            'validity_days' => 30,
            'transaction_type' => MembershipTransaction::APPLICATION,
            'amount' => '150.00',
            'transaction_date' => now(),
            'processed_by' => User::factory()->admin(),
        ];
    }
}
