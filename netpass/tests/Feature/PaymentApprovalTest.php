<?php

namespace Tests\Feature;

use App\Actions\ApproveMembership;
use App\Actions\RecordPayment;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction as Transaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PaymentApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->travelTo(now()->setDate(2026, 11, 22)->startOfDay());
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
        $this->transaction = Transaction::factory()->create(['amount' => '250.50', 'validity_days' => 30]);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'amount' => '250.50', 'method' => 'Cash', 'payment_date' => today()->toDateString(),
            'reference_number' => (string) Str::ulid(), 'idempotency_key' => (string) Str::uuid(), 'verified' => '1',
        ], $overrides);
    }

    private function pay(?Transaction $transaction = null, array $overrides = []): void
    {
        $this->post(route('transactions.payments.store', $transaction ?? $this->transaction), $this->payload($overrides))
            ->assertSessionHasNoErrors()->assertRedirect();
    }

    private function approve(?Transaction $transaction = null): void
    {
        $this->post(route('transactions.approve', $transaction ?? $this->transaction))
            ->assertSessionHasNoErrors()->assertRedirect();
    }

    public function test_partial_payments_become_paid_only_when_fully_verified_and_approval_is_separate(): void
    {
        $before = $this->transaction->member->getAttributes();
        $this->pay(overrides: ['amount' => '100.10', 'method' => 'GCash']);
        $this->assertSame(Transaction::PENDING, $this->transaction->fresh()->status);
        $this->assertSame(15040, $this->transaction->balanceCents());
        $this->post(route('transactions.approve', $this->transaction))->assertSessionHasErrors('approval');
        $this->pay(overrides: ['amount' => '150.40', 'method' => 'Maya']);
        $this->assertSame(Transaction::PAID, $this->transaction->fresh()->status);
        $this->assertNull($this->transaction->fresh()->membership_start_date);
        $this->assertNull($this->transaction->member->fresh()->approvedMembership);
        $this->assertSame($before, $this->transaction->member->fresh()->getAttributes());
        $this->approve();
        $approved = $this->transaction->fresh();
        $this->assertSame(Transaction::APPROVED, $approved->status);
        $this->assertSame('2026-11-22', $approved->membership_start_date->toDateString());
        $this->assertSame('2026-12-22', $approved->membership_end_date->toDateString());
        $this->assertSame($this->admin->id, $approved->approved_by);
        $this->assertNotNull($approved->approved_at);
        $this->assertSame($approved->membership_level_id, $approved->member->approvedMembership->membership_level_id);
        $this->assertSame('Active', $approved->member->paidMembershipStatus());
        $this->assertSame($approved->membership_level_id, $approved->member->membership_level_id);
        $this->assertSame($before['status'], $approved->member->status);
    }

    public function test_all_payment_methods_and_receipts_are_supported(): void
    {
        foreach (Payment::METHODS as $method) {
            $transaction = Transaction::factory()->create(['amount' => '10.00']);
            $this->pay($transaction, ['amount' => '10', 'method' => $method]);
            $payment = $transaction->payments()->firstOrFail();
            $this->assertSame($this->admin->id, $payment->recorded_by);
            $this->assertNotNull($payment->verified_at);
            $this->get(route('payments.receipt', $payment))->assertOk()->assertSee('Print receipt')
                ->assertSee($payment->reference_number)->assertSee($method)->assertSee('10.00');
        }
    }

    public function test_overpayment_invalid_amounts_dates_and_missing_verification_are_rejected(): void
    {
        foreach (['250.51', '0', '-1', '1.001', '1e2', '999999999'] as $amount) {
            $this->post(route('transactions.payments.store', $this->transaction), $this->payload(['amount' => $amount]))->assertSessionHasErrors('amount');
        }
        foreach ([['verified' => '0'], ['method' => 'Bitcoin'], ['payment_date' => '2026-11-23'], ['reference_number' => ''], ['idempotency_key' => 'invalid'], ['recorded_by' => 99]] as $invalid) {
            $this->post(route('transactions.payments.store', $this->transaction), $this->payload($invalid))->assertSessionHasErrors(array_key_first($invalid));
        }
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_duplicate_reference_and_repeated_submission_are_blocked(): void
    {
        $payload = $this->payload(['amount' => '10', 'reference_number' => ' cash-123 ']);
        $this->post(route('transactions.payments.store', $this->transaction), $payload)->assertSessionHasNoErrors();
        $this->post(route('transactions.payments.store', $this->transaction), $payload)->assertSessionHasErrors('reference_number');
        $this->post(route('transactions.payments.store', $this->transaction), array_replace($payload, ['reference_number' => 'DIFFERENT']))->assertSessionHasErrors('reference_number');
        $other = Transaction::factory()->create();
        $this->post(route('transactions.payments.store', $other), $this->payload(['amount' => '1', 'reference_number' => 'CASH-123']))->assertSessionHasErrors('reference_number');
        $this->assertDatabaseCount('payments', 1);
        $this->assertSame(24050, $this->transaction->balanceCents());
    }

    public function test_partial_payment_blocks_cancellation_and_overpayment_of_remaining_balance(): void
    {
        $this->pay(overrides: ['amount' => '200']);
        $this->patch(route('transactions.cancel', $this->transaction))->assertSessionHas('error');
        $this->assertFalse($this->transaction->fresh()->canCancel());
        $this->post(route('transactions.payments.store', $this->transaction), $this->payload(['amount' => '50.51']))->assertSessionHasErrors('amount');
        $this->assertSame(Transaction::PENDING, $this->transaction->fresh()->status);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_cancelled_paid_and_approved_transactions_reject_additional_payments(): void
    {
        foreach ([Transaction::CANCELLED, Transaction::PAID, Transaction::APPROVED, Transaction::EXPIRED] as $status) {
            Transaction::whereKey($this->transaction->id)->update(['status' => $status]);
            $this->post(route('transactions.payments.store', $this->transaction), $this->payload())->assertSessionHasErrors('amount');
        }
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_approval_rechecks_payment_evidence_and_rejects_duplicate_approval(): void
    {
        Transaction::whereKey($this->transaction->id)->update(['status' => Transaction::PAID]);
        $this->post(route('transactions.approve', $this->transaction))->assertSessionHasErrors('approval');
        Payment::factory()->create(['membership_transaction_id' => $this->transaction->id, 'amount' => '250.50', 'verified_at' => null]);
        $this->post(route('transactions.approve', $this->transaction))->assertSessionHasErrors('approval');
        Payment::query()->update(['verified_at' => now()]);
        $this->approve();
        $before = $this->transaction->fresh()->getAttributes();
        $this->travel(1)->days();
        $this->post(route('transactions.approve', $this->transaction))->assertSessionHasErrors('approval');
        $this->assertSame($before, $this->transaction->fresh()->getAttributes());
    }

    public function test_active_renewal_extends_from_expiration_and_uses_snapshot_duration(): void
    {
        $this->pay();
        $this->approve();
        $member = $this->transaction->member;
        $level = MembershipLevel::factory()->create(['fee' => '99.00', 'validity_days' => 45, 'benefits' => 'Access']);
        $this->travel(10)->days();
        $this->post(route('transactions.renewals.store'), ['member_id' => $member->id, 'membership_level_id' => $level->id])->assertSessionHasNoErrors();
        $renewal = Transaction::latest('id')->firstOrFail();
        $this->assertSame($this->transaction->id, $member->fresh()->approvedMembership->id);
        $this->pay($renewal, ['amount' => '99', 'method' => 'Bank Transfer']);
        $level->update(['validity_days' => 100]);
        $this->approve($renewal);
        $this->assertSame('2027-02-05', $renewal->fresh()->membership_end_date->toDateString());
        $this->assertSame('2026-12-02', $renewal->fresh()->membership_start_date->toDateString());
        $this->assertSame('Active', $member->fresh()->paidMembershipStatus());
        $this->assertSame($level->id, $member->fresh()->approvedMembership->membership_level_id);
        $this->assertSame($level->id, $member->fresh()->membership_level_id);
        $this->assertSame('2026-12-22', $this->transaction->fresh()->membership_end_date->toDateString());
    }

    public function test_expired_renewal_starts_at_approval_and_expiration_does_not_change_account_status(): void
    {
        $this->pay();
        $this->approve();
        $this->travelTo(today()->setDate(2026, 12, 22));
        $this->assertSame('Active', $this->transaction->member->fresh()->paidMembershipStatus());
        $this->travel(1)->days();
        $member = $this->transaction->member->fresh();
        $this->assertSame('Expired', $member->paidMembershipStatus());
        $accountStatus = $member->status;
        $renewal = Transaction::factory()->create(['member_id' => $member->id, 'transaction_type' => Transaction::RENEWAL, 'amount' => '250.50', 'validity_days' => 30]);
        $this->pay($renewal);
        $this->approve($renewal);
        $this->assertSame('2026-12-23', $renewal->fresh()->membership_start_date->toDateString());
        $this->assertSame('2027-01-22', $renewal->fresh()->membership_end_date->toDateString());
        $this->assertSame($accountStatus, $member->fresh()->status);
    }

    public function test_first_time_renewal_cannot_be_approved_even_if_paid(): void
    {
        Transaction::whereKey($this->transaction->id)->update(['transaction_type' => Transaction::RENEWAL]);
        $this->pay();
        $this->post(route('transactions.approve', $this->transaction))->assertSessionHasErrors('approval');
        $this->assertNull($this->transaction->fresh()->membership_end_date);
    }

    public function test_zero_fee_membership_requires_explicit_approval_without_fake_payment(): void
    {
        Transaction::whereKey($this->transaction->id)->update(['amount' => '0.00']);
        $this->get(route('transactions.show', $this->transaction))->assertSee('No payment is due');
        $this->approve();
        $this->assertSame('Active', $this->transaction->member->fresh()->paidMembershipStatus());
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_and_approval_ui_shows_balance_history_and_separate_statuses(): void
    {
        $this->get(route('transactions.show', $this->transaction))->assertOk()->assertSee('Record Payment')
            ->assertSee('Remaining balance')->assertSee('Account status')->assertSee('Membership status')->assertDontSee('Approve membership');
        $this->pay();
        $this->get(route('transactions.show', $this->transaction))->assertOk()->assertSee('Approve membership')
            ->assertSee('Printable receipt')->assertDontSee('Record Payment');
        $this->approve();
        $this->get(route('transactions.show', $this->transaction))->assertOk()->assertDontSee('Approve membership')->assertSee($this->admin->name);
    }

    public function test_routes_are_admin_only_and_client_cannot_assign_approval_dates(): void
    {
        $payment = Payment::factory()->create(['membership_transaction_id' => $this->transaction->id]);
        $this->post(route('transactions.approve', $this->transaction), ['membership_end_date' => '2099-01-01'])->assertSessionHasErrors('membership_end_date');
        $this->actingAs(User::factory()->create());
        $this->post(route('transactions.payments.store', $this->transaction), $this->payload())->assertForbidden();
        $this->post(route('transactions.approve', $this->transaction))->assertForbidden();
        $this->get(route('payments.receipt', $payment))->assertForbidden();
        auth()->logout();
        $this->post(route('transactions.payments.store', $this->transaction), $this->payload())->assertRedirect(route('login'));
        $this->post(route('transactions.approve', $this->transaction))->assertRedirect(route('login'));
        $this->get(route('payments.receipt', $payment))->assertRedirect(route('login'));
    }

    public function test_actions_reject_non_administrators(): void
    {
        $user = User::factory()->create();
        foreach ([RecordPayment::class, ApproveMembership::class] as $action) {
            try {
                if ($action === RecordPayment::class) {
                    app($action)->handle($this->transaction, $this->payload(), $user);
                } else {
                    app($action)->handle($this->transaction, $user);
                }
                $this->fail('Unauthorized action was allowed.');
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
            }
        }
    }

    public function test_basic_to_silver_approval_updates_profile_and_directory_only_after_approval(): void
    {
        $basic = MembershipLevel::factory()->create(['name' => 'Basic']);
        $profile = Member::factory()->make(['membership_level_id' => $basic->id]);
        $this->post(route('members.store'), $profile->only([
            'full_name', 'email', 'contact_number', 'address', 'membership_category_id', 'membership_level_id', 'status',
        ]))->assertSessionHasNoErrors();
        $member = Member::where('email', $profile->email)->firstOrFail();
        $this->assertNull($member->approvedMembership);
        $level = MembershipLevel::factory()->create(['name' => 'Silver', 'fee' => '250.50', 'validity_days' => 60, 'benefits' => 'Access']);
        $this->post(route('transactions.applications.store'), ['member_id' => $member->id, 'membership_level_id' => $level->id])->assertSessionHasNoErrors();
        $application = $member->transactions()->firstOrFail();
        $this->assertSame($basic->id, $member->fresh()->membership_level_id);
        $this->pay($application);
        $this->assertSame($basic->id, $member->fresh()->membership_level_id);
        $this->assertSame('Pending', $member->fresh()->paidMembershipStatus());
        $this->approve($application);
        $this->assertSame('Active', $member->fresh()->paidMembershipStatus());
        $this->assertSame($level->id, $member->fresh()->approvedMembership->membership_level_id);
        $this->assertSame($level->id, $member->fresh()->membership_level_id);
        $this->assertSame('2026-11-22', $application->fresh()->membership_start_date->toDateString());
        $this->assertSame('2027-01-21', $application->fresh()->membership_end_date->toDateString());
        $this->get(route('members.show', $member))->assertOk()
            ->assertViewHas('member', fn ($record) => $record->membershipLevel->name === 'Silver');
        $this->get(route('members.index', ['search' => $member->membership_number]))->assertOk()
            ->assertViewHas('members', fn ($records) => $records->count() === 1 && $records->first()->membershipLevel->name === 'Silver');
        $approvedBefore = $application->fresh()->getAttributes();
        $this->post(route('transactions.approve', $application))->assertSessionHasErrors('approval');
        $this->assertSame($approvedBefore, $application->fresh()->getAttributes());
        $this->assertSame($level->id, $member->fresh()->membership_level_id);
    }

    public function test_payment_audit_records_cannot_be_edited_or_deleted(): void
    {
        $this->pay();
        $payment = $this->transaction->payments()->firstOrFail();
        foreach (['update', 'delete'] as $operation) {
            try {
                if ($operation === 'update') {
                    $payment->update(['amount' => '1.00']);
                } else {
                    $payment->delete();
                }
                $this->fail('Payment history was changed.');
            } catch (\LogicException $exception) {
                $this->assertSame('250.50', $payment->fresh()->amount);
                $this->assertModelExists($payment);
            }
        }
    }

    public function test_database_uniqueness_protects_payment_reference_and_submission_key(): void
    {
        $this->pay();
        $payment = $this->transaction->payments()->firstOrFail();
        foreach (['reference_number', 'idempotency_key'] as $field) {
            try {
                Payment::factory()->create([$field => $payment->$field]);
                $this->fail('Database accepted a duplicate payment identifier.');
            } catch (UniqueConstraintViolationException $exception) {
                $this->assertDatabaseCount('payments', 1);
            }
        }
    }

    public function test_approval_rolls_back_if_member_level_update_fails(): void
    {
        $this->pay();
        $before = $this->transaction->fresh()->getAttributes();
        $memberBefore = $this->transaction->member->getAttributes();
        $event = 'eloquent.saving: '.Member::class;
        Event::listen($event, function (): void {
            throw new \RuntimeException('Member update failed');
        });
        try {
            app(ApproveMembership::class)->handle($this->transaction, $this->admin);
            $this->fail('Approval should fail when the member cannot be updated.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Member update failed', $exception->getMessage());
        } finally {
            Event::forget($event);
        }
        $this->assertSame($before, $this->transaction->fresh()->getAttributes());
        $this->assertSame($memberBefore, $this->transaction->member->fresh()->getAttributes());
    }
}
