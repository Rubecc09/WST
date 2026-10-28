<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Database\Seeders\MembershipCategorySeeder;
use Database\Seeders\MembershipLevelSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class MemberDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_seeders_can_be_run_twice(): void
    {
        for ($run = 0; $run < 2; $run++) {
            $this->seed([MembershipCategorySeeder::class, MembershipLevelSeeder::class]);
        }
        $this->assertSame(['Corporate', 'Regular', 'Senior Citizen', 'Student'], MembershipCategory::orderBy('name')->pluck('name')->all());
        $this->assertSame(['Basic', 'Gold', 'Platinum', 'Silver'], MembershipLevel::orderBy('name')->pluck('name')->all());
    }

    public function test_members_have_unique_numbers_and_relationships(): void
    {
        $member = Member::factory()->create();
        $other = Member::factory()->create();
        $this->assertMatchesRegularExpression('/^NP-[0-9A-Z]{26}$/', $member->membership_number);
        $this->assertNotSame($member->membership_number, $other->membership_number);
        $this->assertTrue($member->membershipCategory->members->contains($member));
        $this->assertTrue($member->membershipLevel->members->contains($member));
        $this->assertSame(today()->toDateString(), $member->registration_date->toDateString());
    }

    public function test_updates_and_status_changes_preserve_member_information(): void
    {
        $member = Member::factory()->create();
        $number = $member->membership_number;
        $member->update(['full_name' => 'Updated Member']);
        $member->deactivate();
        $this->assertSame('Inactive', $member->fresh()->status);
        $member->reactivate();
        $this->assertDatabaseHas('members', [
            'id' => $member->id, 'full_name' => 'Updated Member',
            'membership_number' => $number, 'status' => 'Active',
        ]);
    }

    public function test_duplicate_emails_are_rejected(): void
    {
        $member = Member::factory()->create();
        $this->expectException(QueryException::class);
        Member::factory()->create(['email' => $member->email]);
    }

    public function test_duplicate_membership_numbers_are_rejected_by_database(): void
    {
        $member = Member::factory()->create();
        $other = Member::factory()->create();
        $this->expectException(QueryException::class);
        Member::whereKey($other->id)->update(['membership_number' => $member->membership_number]);
    }

    public function test_membership_number_cannot_be_edited(): void
    {
        $member = Member::factory()->create();
        $member->membership_number = 'NP-CHANGED';
        $this->expectException(LogicException::class);
        $member->save();
    }

    public function test_members_cannot_be_deleted_through_model(): void
    {
        $member = Member::factory()->create();
        $this->expectException(LogicException::class);
        $member->delete();
    }

    public function test_member_requires_an_existing_category(): void
    {
        $this->expectException(QueryException::class);
        Member::factory()->create(['membership_category_id' => 999999]);
    }

    public function test_member_requires_an_existing_level(): void
    {
        $this->expectException(QueryException::class);
        Member::factory()->create(['membership_level_id' => 999999]);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $member = Member::factory()->create();
        $this->expectException(QueryException::class);
        $member->membershipCategory->delete();
    }

    public function test_level_in_use_cannot_be_deleted(): void
    {
        $member = Member::factory()->create();
        $this->expectException(QueryException::class);
        $member->membershipLevel->delete();
    }
}
