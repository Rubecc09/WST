@if ($transactions->isEmpty())
    <div class="empty-state"><span class="empty-icon"><x-icon name="id-card" /></span><h3>No transactions found</h3><p>No application or renewal transactions match this view.</p></div>
@else
    <div class="table-scroll">
        <table role="table" class="member-table transaction-table">
            <thead role="rowgroup"><tr role="row"><th scope="col">Reference number</th>@if($showMember ?? true)<th scope="col">Member</th>@endif<th scope="col">Type</th><th scope="col">Level</th><th scope="col">Amount</th><th scope="col">Status</th><th scope="col">Date created</th><th scope="col">Actions</th></tr></thead>
            <tbody role="rowgroup">
                @foreach ($transactions as $transaction)
                    <tr role="row">
                        <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Reference number</span><div class="mobile-cell-content"><a class="transaction-link" href="{{ route('transactions.show', $transaction) }}">{{ $transaction->reference_number }}</a></div></td>
                        @if($showMember ?? true)<td role="cell"><span class="mobile-cell-label" aria-hidden="true">Member</span><div class="mobile-cell-content"><a href="{{ route('members.show', $transaction->member) }}">{{ $transaction->member->full_name }}</a><small class="transaction-secondary">{{ $transaction->member->membership_number }}</small></div></td>@endif
                        <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Type</span><div class="mobile-cell-content">{{ $transaction->transaction_type }}</div></td><td role="cell"><span class="mobile-cell-label" aria-hidden="true">Level</span><div class="mobile-cell-content">{{ $transaction->level_name }}</div></td>
                        <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Amount</span><div class="mobile-cell-content">₱{{ number_format((float) $transaction->amount, 2) }}</div></td>
                        <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Status</span><div class="mobile-cell-content"><x-transaction-status :status="$transaction->status" /></div></td>
                        <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Date created</span><div class="mobile-cell-content">{{ $transaction->transaction_date->format('M d, Y H:i') }}</div></td>
                        <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Actions</span><div class="mobile-cell-content"><div class="catalog-actions"><a class="button button-secondary" href="{{ route('transactions.show', $transaction) }}">View</a>@include('transactions.partials.cancel')</div></div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())<div class="pagination-wrap">{{ $transactions->links() }}</div>@endif
@endif
