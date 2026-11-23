<?php

namespace App\Actions;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateMembershipTransaction
{
    public function handle(array $data, string $type, User $administrator): MembershipTransaction
    {
        abort_unless($administrator->isAdministrator(), 403);
        abort_unless(in_array($type, MembershipTransaction::TYPES, true), 422);

        return DB::transaction(function () use ($data, $type, $administrator): MembershipTransaction {
            // Lock the member to serialize requests, even when no transactions exist yet.
            $member = Member::whereKey($data['member_id'])->lockForUpdate()->firstOrFail();
            $level = MembershipLevel::whereKey($data['membership_level_id'])->lockForUpdate()->firstOrFail();
            if (! $level->isConfigured() || $level->fee < 0) {
                throw ValidationException::withMessages(['membership_level_id' => 'Configure this level’s fee, validity, and benefits before using it.']);
            }
            $hasApproved = $member->approvedMembership()->exists();
            if ($type === MembershipTransaction::RENEWAL && ! $hasApproved) {
                throw ValidationException::withMessages(['member_id' => 'A renewal requires a prior approved transaction. Create an application first.']);
            }
            if ($type === MembershipTransaction::APPLICATION && $hasApproved) {
                throw ValidationException::withMessages(['member_id' => 'This member already has an approved transaction. Create a renewal instead.']);
            }
            if ($member->transactions()->whereIn('status', MembershipTransaction::UNRESOLVED)->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['member_id' => 'This member already has an unresolved membership request.']);
            }

            return MembershipTransaction::create([
                'member_id' => $member->id,
                'membership_level_id' => $level->id,
                'level_name' => $level->name,
                'validity_days' => $level->validity_days,
                'transaction_type' => $type,
                'amount' => $level->fee,
                'transaction_date' => now(),
                'latitude' => $type === MembershipTransaction::APPLICATION ? ($data['latitude'] ?? null) : null,
                'longitude' => $type === MembershipTransaction::APPLICATION ? ($data['longitude'] ?? null) : null,
                'remarks' => $data['remarks'] ?? null,
                'processed_by' => $administrator->id,
            ]);
        }, 3);
    }
}
