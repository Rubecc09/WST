@extends('layouts.app')
@section('title', $transaction->reference_number)
@section('breadcrumb', 'Transaction details')
@section('content')
    <div class="page-heading page-heading-compact">
        <div><a class="back-link" href="{{ route('transactions.index') }}"><x-icon name="arrow-left" /> Back to transactions</a><h1>{{ $transaction->reference_number }}</h1><p class="page-description">{{ $transaction->transaction_type }} transaction</p></div>
        @include('transactions.partials.cancel')
    </div>
    @if($errors->any())<div class="validation-summary" role="alert">{{ $errors->first() }}</div>@endif
    <section class="profile-card">
        <div class="profile-hero"><span class="profile-avatar"><x-icon name="id-card" /></span><div class="profile-main"><div class="profile-name-row"><h2>{{ $transaction->member->full_name }}</h2><x-transaction-status :status="$transaction->status" /></div><a class="transaction-link" href="{{ route('members.show', $transaction->member) }}">{{ $transaction->member->membership_number }}</a></div></div>
        <div class="profile-content">
            <dl class="transaction-details">
                <div><dt>Account status</dt><dd>{{ $transaction->member->status }}</dd></div>
                <div><dt>Membership status</dt><dd>{{ $transaction->member->paidMembershipStatus() }}</dd></div>
                <div><dt>Verified payments</dt><dd>PHP {{ \App\Models\Payment::decimal($transaction->paidCents()) }}</dd></div>
                <div><dt>Remaining balance</dt><dd>PHP {{ \App\Models\Payment::decimal($transaction->balanceCents()) }}</dd></div>
                <div><dt>Approved by</dt><dd>{{ $transaction->approver?->name ?? 'Not recorded' }}</dd></div>
                <div><dt>Approval date</dt><dd>{{ $transaction->approved_at?->format('M d, Y H:i') ?? 'Not recorded' }}</dd></div>
                <div><dt>Current approved level</dt><dd>{{ $transaction->member->approvedMembership?->level_name ?? 'None' }}</dd></div>
                <div><dt>Requested level at creation</dt><dd>{{ $transaction->level_name }}</dd></div>
                <div><dt>Amount</dt><dd>₱{{ number_format((float) $transaction->amount, 2) }}</dd></div>
                <div><dt>Validity at creation</dt><dd>{{ $transaction->validity_days }} days</dd></div>
                <div><dt>Transaction date</dt><dd>{{ $transaction->transaction_date->format('F d, Y H:i') }}</dd></div>
                <div><dt>Created by</dt><dd>{{ $transaction->administrator?->name ?? 'Administrator no longer available' }}</dd></div>
                <div><dt>Member email</dt><dd>{{ $transaction->member->email }}</dd></div>
                <div><dt>Membership start date</dt><dd>{{ $transaction->membership_start_date?->format('F d, Y') ?? 'Not assigned' }}</dd></div>
                <div><dt>Membership end date</dt><dd>{{ $transaction->membership_end_date?->format('F d, Y') ?? 'Not assigned' }}</dd></div>
                <div class="transaction-remarks"><dt>Remarks</dt><dd>{{ $transaction->remarks ?? 'No remarks' }}</dd></div>
            </dl>
            <p class="field-hint">Creating this transaction does not change membership status or dates. Valid payment and approval are required to activate or extend membership.</p>
        </div>
    </section>
    @include('transactions.partials.payments')
@endsection
