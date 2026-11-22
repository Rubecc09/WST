@extends('layouts.app')
@section('title', 'Membership Transactions')
@section('breadcrumb', 'Membership Transactions')
@section('content')
    <div class="page-heading">
        <div><div class="eyebrow"><span class="eyebrow-line"></span> MEMBERSHIP REQUESTS</div><h1>Membership Transactions</h1><p class="page-description">Create applications and renewals, and follow their progress.</p></div>
        <div class="heading-actions"><a class="button button-primary" href="{{ route('transactions.applications.create') }}">New Application</a><a class="button button-secondary" href="{{ route('transactions.renewals.create') }}">New Renewal</a></div>
    </div>
    <section class="directory-card">
        <div class="directory-header"><div><h2>Transaction directory</h2><p>Open a transaction to record verified payments, approve membership, or print receipts.</p></div></div>
        @if($errors->any())<div class="validation-summary" role="alert">{{ $errors->first() }}</div>@endif
        <form class="filter-bar transaction-filters" action="{{ route('transactions.index') }}" method="GET">
            <label class="search-box"><x-icon name="search" /><input name="search" type="search" value="{{ $filters['search'] ?? request('search') }}" placeholder="Reference, member name or membership number" aria-label="Search transactions"></label>
            <label class="filter-select-wrap"><select name="type" aria-label="Transaction type"><option value="">All types</option>@foreach(\App\Models\MembershipTransaction::TYPES as $type)<option @selected(($filters['type'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></label>
            <label class="filter-select-wrap"><select name="status" aria-label="Transaction status"><option value="">All statuses</option>@foreach(\App\Models\MembershipTransaction::STATUSES as $status)<option @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>@endforeach</select></label>
            <label class="transaction-date-filter">From<input class="form-control" name="from" type="date" value="{{ $filters['from'] ?? '' }}"></label>
            <label class="transaction-date-filter">To<input class="form-control" name="to" type="date" value="{{ $filters['to'] ?? '' }}"></label>
            <button class="button button-filter" type="submit">Apply filters</button><a class="clear-filters" href="{{ route('transactions.index') }}">Clear</a>
        </form>
        @include('transactions.partials.table')
    </section>
@endsection
