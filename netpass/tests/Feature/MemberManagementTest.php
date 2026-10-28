<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Database\Seeders\MembershipCategorySeeder;
use Database\Seeders\MembershipLevelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([MembershipCategorySeeder::class, MembershipLevelSeeder::class]);
    }

    public function test_member_directory_shows_statistics_and_applies_search_and_filters(): void
    {
        $student = MembershipCategory::where('name', 'Student')->firstOrFail();
        $regular = MembershipCategory::where('name', 'Regular')->firstOrFail();
        $gold = MembershipLevel::where('name', 'Gold')->firstOrFail();
        $silver = MembershipLevel::where('name', 'Silver')->firstOrFail();

        Member::factory()->create([
            'full_name' => 'Jamie Rivera',
            'membership_category_id' => $student->id,
            'membership_level_id' => $gold->id,
        ]);
        Member::factory()->create([
            'full_name' => 'Jordan Lee',
            'membership_category_id' => $regular->id,
            'membership_level_id' => $silver->id,
            'status' => 'Inactive',
        ]);

        $response = $this->get(route('members.index', [
            'search' => 'Jamie',
            'category' => $student->id,
            'level' => $gold->id,
            'status' => 'Active',
        ]));

        $response->assertOk()
            ->assertSee('Total members')
            ->assertSee('Active members')
            ->assertSee('Inactive members')
            ->assertSee('Jamie Rivera')
            ->assertDontSee('Jordan Lee');
    }

    public function test_member_can_be_created_with_validation_and_viewed(): void
    {
        $category = MembershipCategory::where('name', 'Student')->firstOrFail();
        $level = MembershipLevel::where('name', 'Silver')->firstOrFail();

        $this->get(route('members.create'))
            ->assertOk()
            ->assertSee('Add a member')
            ->assertSee('name="address"', false);

        $this->from(route('members.create'))
            ->post(route('members.store'), [
                'full_name' => 'Taylor Park',
                'email' => 'taylor@example.com',
                'contact_number' => '09123456789',
                'address' => '24 Main Street, Cebu City',
                'membership_category_id' => $category->id,
                'membership_level_id' => $level->id,
                'status' => 'Active',
            ])
            ->assertRedirect();

        $member = Member::where('email', 'taylor@example.com')->firstOrFail();

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'full_name' => 'Taylor Park',
            'status' => 'Active',
            'membership_category_id' => $category->id,
            'membership_level_id' => $level->id,
        ]);

        $this->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Taylor Park')
            ->assertSee($member->membership_number)
            ->assertSee('24 Main Street, Cebu City');
    }

    public function test_member_form_returns_clear_validation_errors(): void
    {
        $response = $this->from(route('members.create'))
            ->post(route('members.store'), [
                'full_name' => '',
                'email' => 'not-an-email',
            ]);

        $response->assertRedirect(route('members.create'))
            ->assertSessionHasErrors(['full_name', 'email', 'contact_number', 'address', 'membership_category_id', 'membership_level_id', 'status']);
    }

    public function test_member_can_be_updated_deactivated_and_reactivated(): void
    {
        $member = Member::factory()->create();

        $this->get(route('members.edit', $member))
            ->assertOk()
            ->assertSee('value="'.$member->full_name.'"', false)
            ->assertSee('Edit member');

        $this->put(route('members.update', $member), [
            'full_name' => 'Updated Member',
            'email' => $member->email,
            'contact_number' => '09987654321',
            'address' => 'Updated address',
            'membership_category_id' => $member->membership_category_id,
            'membership_level_id' => $member->membership_level_id,
            'status' => 'Active',
        ])->assertRedirect(route('members.show', $member));

        $this->patch(route('members.deactivate', $member))
            ->assertRedirect(route('members.index'));
        $this->assertSame('Inactive', $member->fresh()->status);

        $this->patch(route('members.reactivate', $member))
            ->assertRedirect(route('members.index'));
        $this->assertSame('Active', $member->fresh()->status);
    }
}
