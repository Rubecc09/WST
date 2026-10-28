<?php

namespace Database\Seeders;

use App\Models\MembershipLevel;
use Illuminate\Database\Seeder;

class MembershipLevelSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Basic', 'Silver', 'Gold', 'Platinum'] as $name) {
            MembershipLevel::firstOrCreate(['name' => $name]);
        }
    }
}
