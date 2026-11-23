@if ($transaction->canApprove())
    <form class="form-card transaction-history" method="POST" action="{{ route('transactions.approve', $transaction) }}" data-confirm-form data-confirm-title="Approve membership?" data-confirm-message="The requested level will become effective and membership validity will begin or extend." data-confirm-label="Approve membership" data-confirm-note="Approval is recorded in transaction history.">
        @csrf
        <div class="form-footer"><span>{{ $transaction->amount === '0.00' ? 'No payment is due for this zero-fee membership.' : 'Fully paid and verified. Ready for approval.' }}</span><button class="button button-primary" type="submit">Approve membership</button></div>
    </form>
@endif

@if ($transaction->status === \App\Models\MembershipTransaction::PENDING && $transaction->balanceCents() > 0)
    <section class="form-card transaction-history" id="record-payment">
        <div class="form-card-header"><span class="form-section-icon"><x-icon name="id-card" /></span><div><h2>Record Payment</h2><p>Record funds received and verified by an administrator. Partial payments are allowed.</p></div></div>
        <form method="POST" action="{{ route('transactions.payments.store', $transaction) }}">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">
            <div class="form-section form-section-last"><div class="form-grid">
                <x-form.field name="amount" label="Amount (PHP)" :required="true">
                    <input class="form-control" id="amount" name="amount" type="number" min="0.01" max="{{ \App\Models\Payment::decimal($transaction->balanceCents()) }}" step="0.01" value="{{ old('amount', \App\Models\Payment::decimal($transaction->balanceCents())) }}" required>
                </x-form.field>
                <x-form.field name="method" label="Payment method" :required="true">
                    <select class="form-control" id="method" name="method" required>
                        @foreach (\App\Models\Payment::METHODS as $method)
                            <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                        @endforeach
                    </select>
                </x-form.field>
                <x-form.field name="payment_date" label="Payment date" :required="true">
                    <input class="form-control" type="date" id="payment_date" name="payment_date" max="{{ today()->toDateString() }}" value="{{ old('payment_date', today()->toDateString()) }}" required>
                </x-form.field>
                <x-form.field name="reference_number" label="Payment reference number" :required="true" hint="Use the provider reference, or a unique cash receipt number. Letters, numbers, hyphens, slashes, dots and underscores only.">
                    <input class="form-control" id="reference_number" name="reference_number" maxlength="100" value="{{ old('reference_number') }}" required>
                </x-form.field>
                <x-form.field name="verified" label="Verification" :required="true">
                    <label><input type="checkbox" name="verified" id="verified" value="1" required> I verified that these funds were received.</label>
                </x-form.field>
            </div></div>
            <div class="form-footer"><p class="field-hint">Recording payment does not approve membership.</p><button class="button button-primary" type="submit">Record Payment</button></div>
        </form>
    </section>
@endif

<section class="directory-card transaction-history">
    <div class="directory-header"><div><h2>Payment history</h2><p>Verified receipts are retained with this transaction.</p></div></div>
    <div class="table-scroll"><table role="table" class="member-table">
        <thead role="rowgroup"><tr role="row"><th scope="col">Reference</th><th scope="col">Date</th><th scope="col">Method</th><th scope="col">Amount</th><th scope="col">Verification</th><th scope="col">Administrator</th><th scope="col">Receipt</th></tr></thead>
        <tbody role="rowgroup">
            @forelse ($transaction->payments as $payment)
                <tr role="row"><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Reference</span><div class="mobile-cell-content">{{ $payment->reference_number }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Date</span><div class="mobile-cell-content">{{ $payment->payment_date->format('M d, Y') }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Method</span><div class="mobile-cell-content">{{ $payment->method }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Amount</span><div class="mobile-cell-content">PHP {{ $payment->amount }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Verification</span><div class="mobile-cell-content">{{ $payment->verified_at?->format('M d, Y H:i') ?? 'Unverified' }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Administrator</span><div class="mobile-cell-content">{{ $payment->administrator?->name ?? 'Administrator no longer available' }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Receipt</span><div class="mobile-cell-content"><a class="transaction-link" href="{{ route('payments.receipt', $payment) }}">Printable receipt</a></div></td></tr>
            @empty
                <tr role="row"><td colspan="7">No payments recorded.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</section>
