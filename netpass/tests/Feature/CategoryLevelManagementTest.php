<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryLevelManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
        $this->withoutVite();
    }

    private function levelData(array $overrides = []): array
    {
        return array_replace(['name' => 'Weekend', 'description' => 'Test description', 'fee' => '150.50', 'validity_days' => 30, 'benefits' => 'Test privileges'], $overrides);
    }

    public function test_category_can_be_created_listed_and_used_in_member_forms(): void
    {
        $this->get(route('categories.index'))->assertOk()->assertSee('No categories yet');
        $this->get(route('categories.create'))->assertOk()->assertSee('name="name"', false);
        $this->post(route('categories.store'), ['name' => 'Weekend', 'description' => 'Test description'])->assertRedirect(route('categories.index'))->assertSessionHas('success');
        $record = MembershipCategory::where('name', 'Weekend')->firstOrFail();
        $this->assertDatabaseHas('membership_categories', ['id' => $record->id, 'description' => 'Test description']);
        $this->get(route('categories.index'))->assertOk()->assertSee('Weekend');
        $this->get(route('members.create'))->assertOk()->assertSee('Weekend');
        $member = Member::factory()->create();
        $this->get(route('members.edit', $member))->assertOk()->assertSee('Weekend');
    }

    public function test_category_requires_fields_and_preserves_input(): void
    {
        $this->from(route('categories.create'))->post(route('categories.store'), ['description' => 'Keep this'])
            ->assertRedirect(route('categories.create'))->assertSessionHasErrors(['name'])
            ->assertSessionHasInput('description', 'Keep this');
    }

    public function test_category_rejects_case_insensitive_duplicates_on_create_and_update(): void
    {
        $existing = MembershipCategory::factory()->create(['name' => 'Weekend']);
        $other = MembershipCategory::factory()->create(['name' => 'Other']);
        $data = ['name' => 'Weekend', 'description' => 'Test description'];
        $data['name'] = '  wEeKeNd  ';
        $this->post(route('categories.store'), $data)->assertSessionHasErrors('name');
        $this->put(route('categories.update', $other), $data)->assertSessionHasErrors('name');
        $this->assertSame('Other', $other->fresh()->name);
        $data['name'] = 'WEEKEND';
        $this->put(route('categories.update', $existing), $data)->assertSessionHasNoErrors()->assertRedirect(route('categories.index'));
    }

    public function test_category_updates_preserve_assignments_and_display_renames(): void
    {
        $record = MembershipCategory::factory()->create();
        $member = Member::factory()->create(['membership_category_id' => $record->id]);
        $this->get(route('categories.edit', $record))->assertOk()->assertSee($record->name);
        $this->put(route('categories.update', $record), ['name' => 'Renamed', 'description' => 'Updated description'])->assertSessionHasNoErrors()->assertRedirect(route('categories.index'));
        $this->assertSame($record->id, $member->fresh()->membership_category_id);
        $this->get(route('members.index', ['category' => $record->id]))->assertOk()->assertSee('Renamed')->assertSee($member->full_name);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $record = MembershipCategory::factory()->create();
        $this->delete(route('categories.destroy', $record))->assertRedirect(route('categories.index'))->assertSessionHas('success');
        $this->assertModelMissing($record);
        $this->get(route('members.index'))->assertOk();
    }

    public function test_assigned_category_cannot_be_deleted_even_for_inactive_members(): void
    {
        $record = MembershipCategory::factory()->create();
        $member = Member::factory()->create(['membership_category_id' => $record->id, 'status' => 'Inactive']);
        $this->get(route('categories.index'))->assertViewHas('categories', fn ($records) => $records->first()->members_count === 1);
        $this->delete(route('categories.destroy', $record))->assertRedirect(route('categories.index'))->assertSessionHas('error');
        $this->assertModelExists($record);
        $this->assertSame($record->id, $member->fresh()->membership_category_id);
    }

    public function test_level_can_be_created_listed_and_used_in_member_forms(): void
    {
        $this->get(route('levels.index'))->assertOk()->assertSee('No levels yet');
        $this->get(route('levels.create'))->assertOk()->assertSee('name="name"', false);
        $this->post(route('levels.store'), $this->levelData())->assertRedirect(route('levels.index'))->assertSessionHas('success');
        $record = MembershipLevel::where('name', 'Weekend')->firstOrFail();
        $this->assertDatabaseHas('membership_levels', ['id' => $record->id, 'description' => 'Test description']);
        $this->get(route('levels.index'))->assertOk()->assertSee('Weekend')->assertSee('₱150.50')->assertSee('30 days')->assertSee('Test privileges');
        $this->get(route('members.create'))->assertOk()->assertSee('Weekend');
        $member = Member::factory()->create();
        $this->get(route('members.edit', $member))->assertOk()->assertSee('Weekend');
    }

    public function test_level_requires_fields_and_preserves_input(): void
    {
        $this->from(route('levels.create'))->post(route('levels.store'), ['description' => 'Keep this'])
            ->assertRedirect(route('levels.create'))->assertSessionHasErrors(['name', 'fee', 'validity_days', 'benefits'])
            ->assertSessionHasInput('description', 'Keep this');
    }

    public function test_level_rejects_case_insensitive_duplicates_on_create_and_update(): void
    {
        $existing = MembershipLevel::factory()->create(['name' => 'Weekend']);
        $other = MembershipLevel::factory()->create(['name' => 'Other']);
        $data = $this->levelData();
        $data['name'] = '  wEeKeNd  ';
        $this->post(route('levels.store'), $data)->assertSessionHasErrors('name');
        $this->put(route('levels.update', $other), $data)->assertSessionHasErrors('name');
        $this->assertSame('Other', $other->fresh()->name);
        $data['name'] = 'WEEKEND';
        $this->put(route('levels.update', $existing), $data)->assertSessionHasNoErrors()->assertRedirect(route('levels.index'));
    }

    public function test_level_updates_preserve_assignments_and_display_renames(): void
    {
        $record = MembershipLevel::factory()->create();
        $member = Member::factory()->create(['membership_level_id' => $record->id]);
        $this->get(route('levels.edit', $record))->assertOk()->assertSee($record->name);
        $this->put(route('levels.update', $record), $this->levelData(['name' => 'Renamed']))->assertSessionHasNoErrors()->assertRedirect(route('levels.index'));
        $this->assertSame($record->id, $member->fresh()->membership_level_id);
        $this->get(route('members.index', ['level' => $record->id]))->assertOk()->assertSee('Renamed')->assertSee($member->full_name);
    }

    public function test_unused_level_can_be_deleted(): void
    {
        $record = MembershipLevel::factory()->create();
        $this->delete(route('levels.destroy', $record))->assertRedirect(route('levels.index'))->assertSessionHas('success');
        $this->assertModelMissing($record);
        $this->get(route('members.index'))->assertOk();
    }

    public function test_assigned_level_cannot_be_deleted_even_for_inactive_members(): void
    {
        $record = MembershipLevel::factory()->create();
        $member = Member::factory()->create(['membership_level_id' => $record->id, 'status' => 'Inactive']);
        $this->get(route('levels.index'))->assertViewHas('levels', fn ($records) => $records->first()->members_count === 1);
        $this->delete(route('levels.destroy', $record))->assertRedirect(route('levels.index'))->assertSessionHas('error');
        $this->assertModelExists($record);
        $this->assertSame($record->id, $member->fresh()->membership_level_id);
    }

    public function test_invalid_level_settings_are_rejected_on_create_and_update(): void
    {
        $level = MembershipLevel::factory()->create();
        foreach ([
            ['fee', '-1'], ['fee', 'abc'], ['fee', '1.001'], ['fee', '100000000'],
            ['validity_days', 0], ['validity_days', -1], ['validity_days', '1.5'], ['validity_days', 'abc'],
            ['benefits', '   '],
        ] as [$field, $value]) {
            $data = $this->levelData([$field => $value]);
            $this->post(route('levels.store'), $data)->assertSessionHasErrors($field);
            $this->put(route('levels.update', $level), $data)->assertSessionHasErrors($field);
        }
    }

    public function test_zero_fee_is_valid_and_legacy_levels_are_identified(): void
    {
        $legacy = MembershipLevel::factory()->create(['name' => 'Legacy']);
        $this->get(route('levels.index'))->assertOk()->assertSee('Needs configuration')->assertSee('Not configured');
        $this->get(route('levels.edit', $legacy))->assertOk()->assertSee('Needs configuration');
        $this->put(route('levels.update', $legacy), $this->levelData(['fee' => 0]))->assertSessionHasNoErrors();
        $this->assertSame('0.00', $legacy->fresh()->fee);
        $this->assertTrue($legacy->fresh()->isConfigured());
    }

    public function test_name_and_number_search_remain_grouped_with_all_filters(): void
    {
        $member = Member::factory()->create(['full_name' => 'Search Target', 'status' => 'Active']);
        Member::factory()->create(['full_name' => 'Search Target Excluded', 'status' => 'Inactive']);
        foreach (['Search Target', $member->membership_number] as $search) {
            $filters = ['search' => $search, 'category' => $member->membership_category_id, 'level' => $member->membership_level_id, 'status' => 'Active'];
            $this->get(route('members.index', $filters))->assertOk()
                ->assertViewHas('members', fn ($members) => $members->count() === 1 && $members->first()->is($member));
            $filters['status'] = 'Inactive';
            $this->get(route('members.index', $filters))->assertOk()
                ->assertViewHas('members', fn ($members) => $members->isEmpty());
        }
    }
}
