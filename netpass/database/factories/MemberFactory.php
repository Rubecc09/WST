<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Member> */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'contact_number' => fake()->numerify('09#########'),
            'address' => fake()->address(),
            'membership_category_id' => MembershipCategory::factory(),
            'membership_level_id' => MembershipLevel::factory(),
            'registration_date' => today(),
            'status' => 'Active',
        ];
    }
}
