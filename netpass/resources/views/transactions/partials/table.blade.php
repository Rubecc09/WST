@if ($transactions->isEmpty())
    <div class="empty-state"><span class="empty-icon"><x-icon name="id-card" /></span><h3>No transactions found</h3><p>No application or renewal transactions match this view.</p></div>
@else
    <div class="table-scroll">
        <table class="member-table transaction-table">
            <thead><tr><th scope="col">Reference number</th>@if($showMember ?? true)<th scope="col">Member</th>@endif<th scope="col">Type</th><th scope="col">Level</th><th scope="col">Amount</th><th scope="col">Status</th><th scope="col">Date created</th><th scope="col">Actions</th></tr></thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td><a class="transaction-link" href="{{ route('transactions.show', $transaction) }}">{{ $transaction->reference_number }}</a></td>
                        @if($showMember ?? true)<td><a href="{{ route('members.show', $transaction->member) }}">{{ $transaction->member->full_name }}</a><small class="transaction-secondary">{{ $transaction->member->membership_number }}</small></td>@endif
                        <td>{{ $transaction->transaction_type }}</td><td>{{ $transaction->level_name }}</td>
                        <td>₱{{ number_format((float) $transaction->amount, 2) }}</td>
                        <td><x-transaction-status :status="$transaction->status" /></td>
                        <td>{{ $transaction->transaction_date->format('M d, Y H:i') }}</td>
                        <td><div class="catalog-actions"><a class="button button-secondary" href="{{ route('transactions.show', $transaction) }}">View</a>@include('transactions.partials.cancel')</div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())<div class="pagination-wrap">{{ $transactions->links() }}</div>@endif
@endif
