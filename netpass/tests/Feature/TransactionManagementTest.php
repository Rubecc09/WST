<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction as Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class TransactionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Member $member;

    private MembershipLevel $level;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
        $this->member = Member::factory()->create(['status' => 'Inactive']);
        $this->level = MembershipLevel::factory()->create(['fee' => '250.50', 'validity_days' => 60, 'benefits' => 'Test benefits']);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace(['member_id' => $this->member->id, 'membership_level_id' => $this->level->id, 'remarks' => 'Test request'], $overrides);
    }

    private function priorApproved(): Transaction
    {
        $prior = Transaction::factory()->create(['member_id' => $this->member->id]);
        // Preserve support for historical approved records without payment audit data.
        Transaction::whereKey($prior->id)->update(['status' => Transaction::APPROVED]);

        return $prior->fresh();
    }

    public function test_application_uses_server_fee_and_unique_reference_without_changing_member(): void
    {
        $before = $this->member->fresh()->getAttributes();
        $this->post(route('transactions.applications.store'), $this->payload(['amount' => '0.01', 'transaction_type' => Transaction::RENEWAL]))
            ->assertSessionHasNoErrors()->assertRedirect();
        $transaction = Transaction::firstOrFail();
        $this->assertSame(Transaction::APPLICATION, $transaction->transaction_type);
        $this->assertSame(Transaction::PENDING, $transaction->status);
        $this->assertSame('250.50', $transaction->amount);
        $this->assertMatchesRegularExpression('/^APP-[0-9]{4}-[0-9]{6,}$/', $transaction->reference_number);
        $this->assertSame($this->admin->id, $transaction->processed_by);
        $this->assertNull($transaction->membership_start_date);
        $this->assertNull($transaction->membership_end_date);
        $this->assertSame($before, $this->member->fresh()->getAttributes());
        $other = Member::factory()->create();
        $this->post(route('transactions.applications.store'), $this->payload(['member_id' => $other->id]))->assertSessionHasNoErrors();
        $this->assertSame(2, Transaction::distinct()->count('reference_number'));
    }

    public function test_application_rejects_missing_invalid_or_incomplete_inputs(): void
    {
        $this->post(route('transactions.applications.store'), [])->assertSessionHasErrors(['member_id', 'membership_level_id']);
        $this->post(route('transactions.applications.store'), $this->payload(['member_id' => 999999]))->assertSessionHasErrors('member_id');
        $this->post(route('transactions.applications.store'), $this->payload(['membership_level_id' => 999999]))->assertSessionHasErrors('membership_level_id');
        foreach (['fee', 'validity_days', 'benefits'] as $field) {
            $incomplete = MembershipLevel::factory()->create(['fee' => 10, 'validity_days' => 30, 'benefits' => 'Benefits', $field => null]);
            $this->post(route('transactions.applications.store'), $this->payload(['membership_level_id' => $incomplete->id]))->assertSessionHasErrors('membership_level_id');
        }
        $this->assertDatabaseCount('membership_transactions', 0);
    }

    public function test_unresolved_applications_are_blocked_but_cancellation_allows_retry(): void
    {
        $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasNoErrors();
        $transaction = Transaction::firstOrFail();
        foreach (Transaction::UNRESOLVED as $status) {
            Transaction::whereKey($transaction->id)->update(['status' => $status]);
            $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasErrors('member_id');
        }
        Transaction::whereKey($transaction->id)->update(['status' => Transaction::PENDING]);
        $this->patch(route('transactions.cancel', $transaction))->assertSessionHas('success');
        $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertDatabaseCount('membership_transactions', 2);
    }

    public function test_renewals_require_approved_history_and_do_not_extend_membership(): void
    {
        $this->member->update(['status' => 'Active']);
        $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasErrors('member_id');
        $prior = $this->priorApproved();
        $before = $this->member->fresh()->getAttributes();
        $this->post(route('transactions.renewals.store'), $this->payload(['amount' => 1]))->assertSessionHasNoErrors();
        $renewal = Transaction::where('transaction_type', Transaction::RENEWAL)->firstOrFail();
        $this->assertStringStartsWith('REN-', $renewal->reference_number);
        $this->assertSame('250.50', $renewal->amount);
        $this->assertSame(Transaction::PENDING, $renewal->status);
        $this->assertNull($renewal->membership_end_date);
        $this->assertSame($before, $this->member->fresh()->getAttributes());
        $this->assertSame(Transaction::APPROVED, $prior->fresh()->status);
        $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasErrors('member_id');
        Transaction::whereKey($renewal->id)->update(['status' => Transaction::PAID]);
        $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasErrors('member_id');
        $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasErrors('member_id');
    }

    public function test_invalid_renewal_inputs_are_rejected(): void
    {
        $this->post(route('transactions.renewals.store'), $this->payload(['member_id' => 999999]))->assertSessionHasErrors('member_id');
        $this->post(route('transactions.renewals.store'), $this->payload(['membership_level_id' => 999999]))->assertSessionHasErrors('membership_level_id');
        $this->priorApproved();
        $level = MembershipLevel::factory()->create();
        $this->post(route('transactions.renewals.store'), $this->payload(['membership_level_id' => $level->id]))->assertSessionHasErrors('membership_level_id');
    }

    public function test_only_pending_transactions_can_be_cancelled_and_history_is_retained(): void
    {
        $transaction = Transaction::factory()->create();
        $reference = $transaction->reference_number;
        $this->patch(route('transactions.cancel', $transaction))->assertSessionHas('success');
        $this->assertSame(Transaction::CANCELLED, $transaction->fresh()->status);
        $this->assertSame($reference, $transaction->fresh()->reference_number);
        $this->assertModelExists($transaction);
        foreach ([Transaction::CANCELLED, Transaction::PAID, Transaction::APPROVED, Transaction::EXPIRED] as $status) {
            Transaction::whereKey($transaction->id)->update(['status' => $status]);
            $this->patch(route('transactions.cancel', $transaction))->assertSessionHas('error');
            $this->assertSame($status, $transaction->fresh()->status);
        }
    }

    public function test_client_cannot_assign_status_references_or_membership_dates(): void
    {
        foreach (['status' => Transaction::APPROVED, 'reference_number' => 'FAKE', 'processed_by' => $this->admin->id, 'membership_end_date' => '2099-01-01'] as $field => $value) {
            $this->post(route('transactions.applications.store'), $this->payload([$field => $value]))->assertSessionHasErrors($field);
        }
        $this->post(route('transactions.applications.store'), $this->payload(['status' => Transaction::PAID]))->assertSessionHasErrors('status');
        $transaction = Transaction::factory()->create();
        $this->patch(route('transactions.cancel', $transaction), ['status' => Transaction::APPROVED])->assertSessionHasErrors('status');
        $this->patch('/transactions/'.$transaction->id, ['status' => Transaction::PAID])->assertStatus(405);
        $this->assertSame(Transaction::PENDING, $transaction->fresh()->status);
    }

    public function test_historical_snapshots_and_relationships_survive_level_edits(): void
    {
        $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasNoErrors();
        $transaction = Transaction::firstOrFail();
        $oldName = $this->level->name;
        $this->level->update(['name' => 'Updated level', 'fee' => 999, 'validity_days' => 90]);
        $this->assertSame('250.50', $transaction->fresh()->amount);
        $this->assertSame(60, $transaction->fresh()->validity_days);
        $this->assertSame($oldName, $transaction->fresh()->level_name);
        $this->assertTrue($transaction->member->is($this->member));
        $this->assertTrue($transaction->membershipLevel->is($this->level));
        $this->assertTrue($this->admin->processedTransactions->contains($transaction));
        $this->delete(route('levels.destroy', $this->level))->assertSessionHas('error');
        $this->assertModelExists($this->level);
    }

    public function test_transaction_reference_and_status_cannot_be_edited_on_the_model(): void
    {
        $transaction = Transaction::factory()->create();
        $transaction->reference_number = 'CHANGED';
        $this->expectException(LogicException::class);
        $transaction->save();
    }

    public function test_model_rejects_direct_approval(): void
    {
        $transaction = Transaction::factory()->create();
        $transaction->status = Transaction::APPROVED;
        $this->expectException(LogicException::class);
        $transaction->save();
    }

    public function test_transaction_details_and_history_are_scoped_to_the_member(): void
    {
        $transaction = Transaction::factory()->create(['member_id' => $this->member->id, 'processed_by' => $this->admin->id, 'remarks' => 'History note']);
        $other = Transaction::factory()->create();
        $this->get(route('transactions.show', $transaction))->assertOk()->assertSee($transaction->reference_number)->assertSee('History note')->assertSee($this->admin->name);
        $this->get(route('members.show', $this->member))->assertOk()->assertSee('Transaction History')
            ->assertSee($transaction->reference_number)->assertDontSee($other->reference_number);
        $empty = Member::factory()->create();
        $this->get(route('members.show', $empty))->assertSee('No transactions found');
    }

    public function test_search_and_filters_work_together(): void
    {
        $target = Transaction::factory()->create(['member_id' => $this->member->id, 'transaction_date' => '2026-11-22 12:00:00']);
        Transaction::factory()->create(['member_id' => $this->member->id, 'transaction_type' => Transaction::RENEWAL, 'transaction_date' => '2026-11-22 12:00:00']);
        foreach ([$target->reference_number, $this->member->full_name, $this->member->membership_number] as $search) {
            $filters = ['search' => $search, 'type' => Transaction::APPLICATION, 'status' => Transaction::PENDING, 'from' => '2026-11-22', 'to' => '2026-11-22'];
            $this->get(route('transactions.index', $filters))->assertOk()->assertViewHas('transactions', fn ($records) => $records->count() === 1 && $records->first()->is($target));
            $filters['status'] = Transaction::CANCELLED;
            $this->get(route('transactions.index', $filters))->assertOk()->assertViewHas('transactions', fn ($records) => $records->isEmpty());
        }
        $this->get(route('transactions.index', ['to' => '2026-11-22']))->assertOk();
        $this->get(route('transactions.index', ['from' => '2026-11-23', 'to' => '2026-11-22']))->assertSessionHasErrors('to');
    }

    public function test_forms_render_members_levels_and_renewal_guidance(): void
    {
        $this->get(route('transactions.applications.create'))->assertOk()
            ->assertSee($this->member->membership_number)->assertSee('Assigned/requested level:')
            ->assertSee('Approved level: None')->assertSee($this->level->name)->assertSee('250.50');
        $this->get(route('transactions.renewals.create'))->assertOk()
            ->assertDontSee($this->member->membership_number)->assertSee('Renewals require a prior Approved transaction');
        $this->priorApproved();
        $this->get(route('transactions.renewals.create'))->assertSee($this->member->membership_number);
        $this->get(route('transactions.applications.create'))->assertDontSee($this->member->membership_number);
    }

    public function test_profile_assignment_and_pending_or_paid_requests_never_establish_approved_membership(): void
    {
        $this->assertSame('Pending', $this->member->paidMembershipStatus());
        $this->assertNull($this->member->approvedMembership);
        $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasNoErrors();
        $request = Transaction::firstOrFail();
        foreach ([Transaction::PENDING, Transaction::PAID] as $status) {
            Transaction::whereKey($request->id)->update(['status' => $status]);
            $member = $this->member->fresh();
            $this->assertNull($member->approvedMembership);
            $this->assertSame('Pending', $member->paidMembershipStatus());
            $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasErrors('member_id');
            $this->get(route('transactions.applications.create'))->assertDontSee($member->membership_number);
        }
    }

    public function test_expired_approved_history_allows_renewal_without_changing_level_or_dates(): void
    {
        $prior = $this->priorApproved();
        Transaction::whereKey($prior->id)->update([
            'status' => Transaction::EXPIRED,
            'membership_start_date' => today()->subDays(60),
            'membership_end_date' => today()->subDay(),
        ]);
        $before = $prior->fresh()->getAttributes();
        $this->assertSame('Expired', $this->member->fresh()->paidMembershipStatus());
        $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertSame($before, $prior->fresh()->getAttributes());
        $this->assertTrue($this->member->fresh()->approvedMembership->is($prior));
        $this->post(route('transactions.applications.store'), $this->payload())->assertSessionHasErrors('member_id');
    }

    public function test_unresolved_requests_block_other_transaction_types(): void
    {
        $this->priorApproved();
        Transaction::factory()->create(['member_id' => $this->member->id, 'transaction_type' => Transaction::APPLICATION]);
        $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasErrors('member_id');
        $this->assertDatabaseCount('membership_transactions', 2);
    }

    public function test_paid_membership_status_uses_approved_dates_independently_of_account_status(): void
    {
        $prior = $this->priorApproved();
        $this->assertSame('Pending', $this->member->fresh()->paidMembershipStatus());
        Transaction::whereKey($prior->id)->update([
            'membership_start_date' => today(), 'membership_end_date' => today()->addDays(30),
        ]);
        $this->assertSame('Active', $this->member->fresh()->paidMembershipStatus());
        $this->post(route('transactions.renewals.store'), $this->payload())->assertSessionHasNoErrors();
        $member = $this->member->fresh();
        $this->assertSame('Inactive', $member->status);
        $this->assertSame('Active', $member->paidMembershipStatus());
        $this->assertTrue($member->approvedMembership->is($prior));
        $this->get(route('members.show', $member))->assertOk()->assertSee('Paid membership status')
            ->assertSee('Approved level')->assertSee($prior->level_name)->assertSee($this->level->name);
        $this->travel(31)->days();
        $this->assertSame('Expired', $member->fresh()->paidMembershipStatus());
    }
}
