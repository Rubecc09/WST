<?php

namespace App\Actions;

use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApproveMembership
{
    public function handle(MembershipTransaction $transaction, User $administrator): void
    {
        abort_unless($administrator->isAdministrator(), 403);

        DB::transaction(function () use ($transaction, $administrator): void {
            $member = Member::whereKey($transaction->member_id)->lockForUpdate()->firstOrFail();
            $transaction = MembershipTransaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();
            $free = $transaction->amount === '0.00';
            if ((! $free && $transaction->status !== MembershipTransaction::PAID)
                || ($free && ! in_array($transaction->status, MembershipTransaction::UNRESOLVED, true))
                || $transaction->balanceCents() !== 0
                || (! $free && ! $transaction->payments()->whereNotNull('verified_at')->exists())) {
                throw ValidationException::withMessages(['approval' => 'Only fully paid and verified transactions can be approved once.']);
            }
            if ($transaction->validity_days < 1) {
                throw ValidationException::withMessages(['approval' => 'The transaction must have a valid membership duration.']);
            }
            $prior = $member->approvedMembership;
            if (($transaction->transaction_type === MembershipTransaction::RENEWAL && ! $prior)
                || ($transaction->transaction_type === MembershipTransaction::APPLICATION && $prior)) {
                throw ValidationException::withMessages(['approval' => 'The transaction type does not match this member\'s approved history.']);
            }
            $start = today();
            $base = $start->copy();
            if ($transaction->transaction_type === MembershipTransaction::RENEWAL
                && $member->paidMembershipStatus() === 'Active') {
                $base = $prior->membership_end_date->copy();
            }
            MembershipTransaction::whereKey($transaction->id)->update([
                'status' => MembershipTransaction::APPROVED,
                'membership_start_date' => $start,
                'membership_end_date' => $base->addDays($transaction->validity_days),
                'approved_at' => now(),
                'approved_by' => $administrator->id,
            ]);
            $member->update(['membership_level_id' => $transaction->membership_level_id]);
        }, 3);
    }
}
