<?php

namespace App\Http\Controllers;

use App\Actions\ApproveMembership;
use App\Actions\RecordPayment;
use App\Http\Requests\PaymentRequest;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function store(PaymentRequest $request, MembershipTransaction $transaction, RecordPayment $action): RedirectResponse
    {
        try {
            $action->handle($transaction, $request->validated(), $request->user());
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['reference_number' => 'This payment has already been recorded.']);
        }

        return to_route('transactions.show', $transaction)->with('success', 'Verified payment recorded.');
    }

    public function approve(Request $request, MembershipTransaction $transaction, ApproveMembership $action): RedirectResponse
    {
        $request->validate([
            'status' => ['prohibited'], 'approved_by' => ['prohibited'],
            'membership_start_date' => ['prohibited'], 'membership_end_date' => ['prohibited'],
        ]);
        $action->handle($transaction, $request->user());

        return to_route('transactions.show', $transaction)->with('success', 'Membership approved. The requested level is now effective.');
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['transaction.member', 'administrator']);

        return view('transactions.receipt', compact('payment'));
    }
}
