@if ($transaction->canCancel())
    <form action="{{ route('transactions.cancel', $transaction) }}" method="POST" data-confirm-form data-confirm-title="Cancel {{ $transaction->reference_number }}?" data-confirm-message="This Pending transaction will be cancelled. Its record will remain in transaction history." data-confirm-label="Cancel transaction" data-confirm-note="Cancellation does not change the member’s status or membership dates.">
        @csrf
        @method('PATCH')
        <button class="button button-danger-outline" type="submit">Cancel transaction</button>
    </form>
@endif
