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
                <table class="member-table catalog-table">
                    <thead><tr><th scope="col">Level name</th><th scope="col">Membership fee</th><th scope="col">Validity period</th><th scope="col">Benefits</th><th scope="col">Total members</th><th scope="col">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($levels as $level)
                            <tr>
                                <td><strong>{{ $level->name }}</strong>
                                    @if (! $level->isConfigured())<span class="configuration-badge">Needs configuration</span>@endif
                                    @if ($level->description)<p class="catalog-description">{{ $level->description }}</p>@endif</td>
                                <td>{{ $level->fee !== null ? '₱'.number_format((float) $level->fee, 2) : 'Not configured' }}</td>
                                <td>{{ $level->validity_days !== null ? $level->validity_days.' days' : 'Not configured' }}</td>
                                <td class="catalog-text">{{ $level->benefits ?? 'Not configured' }}</td>
                                <td>{{ $level->members_count }}</td>
                                <td>
                                    <div class="catalog-actions">
                                        <a class="button button-secondary" href="{{ route('levels.edit', $level) }}">Edit<span class="sr-only"> {{ $level->name }}</span></a>
                                        <form action="{{ route('levels.destroy', $level) }}" method="POST" data-confirm-form data-confirm-title="Delete {{ $level->name }}?" data-confirm-message="This level will be permanently deleted. Deletion cannot be undone. Assigned members will never be deleted." data-confirm-label="Delete level" data-confirm-note="Only unused levels can be deleted.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button button-danger-outline" type="submit">Delete<span class="sr-only"> {{ $level->name }}</span></button>
                                        </form>
                                    </div>
                                </td>
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
