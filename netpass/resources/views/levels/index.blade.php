@extends('layouts.app')
@section('title', 'Membership Levels')
@section('breadcrumb', 'Membership Levels')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-line"></span> MEMBERSHIP SETTINGS</div>
            <h1>Membership Levels</h1>
            <p class="page-description">Manage fees, validity periods, and membership benefits.</p>
        </div>
        <a class="button button-primary" href="{{ route('levels.create') }}"><x-icon name="plus" /> Add Level</a>
    </div>
    <div class="summary-grid catalog-summary">
        <article class="summary-card">
            <span class="summary-icon summary-icon-blue"><x-icon name="sparkles" /></span>
            <div class="summary-label">Total Levels</div>
            <div class="summary-value">{{ $levels->total() }}</div>
            <div class="summary-foot">Available for member assignment</div>
        </article>
    </div>
    <section class="directory-card">
        <div class="directory-header">
            <div><h2>Levels directory</h2><p>Levels used by members or transaction history cannot be deleted.</p></div>
        </div>
        @if ($levels->isEmpty())
            <div class="empty-state"><span class="empty-icon"><x-icon name="sparkles" /></span><h3>No levels yet</h3><p>Add a level to make it available for member registration.</p><a class="button button-primary" href="{{ route('levels.create') }}">Add Level</a></div>
        @else
            <div class="table-scroll">
                <table role="table" class="member-table catalog-table">
                    <thead role="rowgroup"><tr role="row"><th scope="col">Level name</th><th scope="col">Membership fee</th><th scope="col">Validity period</th><th scope="col">Benefits</th><th scope="col">Total members</th><th scope="col">Actions</th></tr></thead>
                    <tbody role="rowgroup">
                        @foreach ($levels as $level)
                            <tr role="row">
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Level name</span><div class="mobile-cell-content"><strong>{{ $level->name }}</strong>
                                    @if (! $level->isConfigured())<span class="configuration-badge">Needs configuration</span>@endif
                                    @if ($level->description)<p class="catalog-description">{{ $level->description }}</p>@endif</div></td>
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Membership fee</span><div class="mobile-cell-content">{{ $level->fee !== null ? '₱'.number_format((float) $level->fee, 2) : 'Not configured' }}</div></td>
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Validity period</span><div class="mobile-cell-content">{{ $level->validity_days !== null ? $level->validity_days.' days' : 'Not configured' }}</div></td>
                                <td role="cell" class="catalog-text"><span class="mobile-cell-label" aria-hidden="true">Benefits</span><div class="mobile-cell-content">{{ $level->benefits ?? 'Not configured' }}</div></td>
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Total members</span><div class="mobile-cell-content">{{ $level->members_count }}</div></td>
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Actions</span><div class="mobile-cell-content">
                                    <div class="catalog-actions">
                                        <a class="button button-secondary" href="{{ route('levels.edit', $level) }}">Edit<span class="sr-only"> {{ $level->name }}</span></a>
                                        <form action="{{ route('levels.destroy', $level) }}" method="POST" data-confirm-form data-confirm-title="Delete {{ $level->name }}?" data-confirm-message="This level will be permanently deleted. Deletion cannot be undone. Assigned members will never be deleted." data-confirm-label="Delete level" data-confirm-note="Only unused levels can be deleted.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button button-danger-outline" type="submit">Delete<span class="sr-only"> {{ $level->name }}</span></button>
                                        </form>
                                    </div>
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($levels->hasPages())
                <div class="pagination-wrap">{{ $levels->links() }}</div>
            @endif
        @endif
    </section>
@endsection
