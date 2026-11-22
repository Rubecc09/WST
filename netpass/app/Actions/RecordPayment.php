<?php

namespace App\Actions;

use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordPayment
{
    public function handle(MembershipTransaction $transaction, array $data, User $administrator): Payment
    {
        abort_unless($administrator->isAdministrator(), 403);

        return DB::transaction(function () use ($transaction, $data, $administrator): Payment {
            $transaction = MembershipTransaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();
            if ($transaction->status !== MembershipTransaction::PENDING) {
                throw ValidationException::withMessages(['amount' => 'Only Pending transactions can receive payments.']);
            }
            $reference = strtoupper(trim($data['reference_number']));
            if (Payment::where('idempotency_key', $data['idempotency_key'])->exists()
                || Payment::where('method', $data['method'])->where('reference_number', $reference)->exists()) {
                throw ValidationException::withMessages(['reference_number' => 'This payment has already been recorded.']);
            }
            $amount = Payment::cents((string) $data['amount']);
            if ($amount <= 0 || $amount > $transaction->balanceCents()) {
                throw ValidationException::withMessages(['amount' => 'Payment must be positive and cannot exceed the remaining balance.']);
            }
            if (! filter_var($data['verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                throw ValidationException::withMessages(['verified' => 'Confirm that the funds were received before recording payment.']);
            }
            $payment = Payment::create([
                'membership_transaction_id' => $transaction->id,
                'amount' => Payment::decimal($amount),
                'method' => $data['method'],
                'payment_date' => $data['payment_date'],
                'reference_number' => $reference,
                'idempotency_key' => $data['idempotency_key'],
                'recorded_by' => $administrator->id,
                'verified_at' => now(),
            ]);
            if ($transaction->balanceCents() === 0) {
                MembershipTransaction::whereKey($transaction->id)->update(['status' => MembershipTransaction::PAID]);
            }

            return $payment;
        }, 3);
    }
}
