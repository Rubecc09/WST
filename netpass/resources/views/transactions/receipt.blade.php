@extends('layouts.app')
@section('title', 'Payment receipt '.$payment->id)
@section('breadcrumb', 'Payment receipt')
@push('styles')
<style>
@media print {
    .sidebar, .topbar, .receipt-actions, .flash-message { display: none !important; }
    .app-shell, .main-column, .page-content { display: block !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
    body, .profile-card, .profile-content { background: white !important; color: black !important; box-shadow: none !important; }
    .transaction-details dt, .transaction-details dd, .field-hint { color: black !important; }
    .profile-card { border: 1px solid #999; break-inside: avoid; }
}
</style>
@endpush
@section('content')
    <div class="page-heading receipt-actions"><a class="button button-secondary" href="{{ route('transactions.show', $payment->transaction) }}">Back to transaction</a><button class="button button-primary" type="button" onclick="window.print()">Print receipt</button></div>
    <section class="profile-card"><div class="profile-content">
        <h1>NetPass Payment Receipt</h1>
        <dl class="transaction-details">
            <div><dt>Receipt number</dt><dd>PAY-{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</dd></div>
            <div><dt>Transaction</dt><dd>{{ $payment->transaction->reference_number }}</dd></div>
            <div><dt>Member</dt><dd>{{ $payment->transaction->member->full_name }} / {{ $payment->transaction->member->membership_number }}</dd></div>
            <div><dt>Requested membership</dt><dd>{{ $payment->transaction->level_name }}</dd></div>
            <div><dt>Payment amount</dt><dd>PHP {{ $payment->amount }}</dd></div>
            <div><dt>Payment method</dt><dd>{{ $payment->method }}</dd></div>
            <div><dt>Payment date</dt><dd>{{ $payment->payment_date->format('F d, Y') }}</dd></div>
            <div><dt>Payment reference</dt><dd>{{ $payment->reference_number }}</dd></div>
            <div><dt>Verified at</dt><dd>{{ $payment->verified_at?->format('F d, Y H:i') ?? 'Unverified' }}</dd></div>
            <div><dt>Recorded and verified by</dt><dd>{{ $payment->administrator?->name ?? 'Administrator no longer available' }}</dd></div>
        </dl>
        <p class="field-hint">This receipt confirms this payment only. Membership activation requires separate approval.</p>
    </div></section>
@endsection
