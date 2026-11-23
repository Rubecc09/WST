@extends('layouts.app')
@section('title', 'Membership Categories')
@section('breadcrumb', 'Membership Categories')
@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-line"></span> MEMBERSHIP SETTINGS</div>
            <h1>Membership Categories</h1>
            <p class="page-description">Manage the categories available when registering members.</p>
        </div>
        <a class="button button-primary" href="{{ route('categories.create') }}"><x-icon name="plus" /> Add Category</a>
    </div>
    <div class="summary-grid catalog-summary">
        <article class="summary-card">
            <span class="summary-icon summary-icon-blue"><x-icon name="users" /></span>
            <div class="summary-label">Total Categories</div>
            <div class="summary-value">{{ $categories->total() }}</div>
            <div class="summary-foot">Available for member assignment</div>
        </article>
    </div>
    <section class="directory-card">
        <div class="directory-header">
            <div><h2>Categories directory</h2><p>Assigned categories cannot be deleted, including those used by inactive members.</p></div>
        </div>
        @if ($categories->isEmpty())
            <div class="empty-state"><span class="empty-icon"><x-icon name="users" /></span><h3>No categories yet</h3><p>Add a category to make it available for member registration.</p><a class="button button-primary" href="{{ route('categories.create') }}">Add Category</a></div>
        @else
            <div class="table-scroll">
                <table role="table" class="member-table catalog-table">
                    <thead role="rowgroup"><tr role="row"><th scope="col">Category name</th><th scope="col">Description</th><th scope="col">Total members</th><th scope="col">Actions</th></tr></thead>
                    <tbody role="rowgroup">
                        @foreach ($categories as $category)
                            <tr role="row">
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Category name</span><div class="mobile-cell-content"><strong>{{ $category->name }}</strong></div></td>
                                <td role="cell" class="catalog-text"><span class="mobile-cell-label" aria-hidden="true">Description</span><div class="mobile-cell-content">{{ $category->description ?? 'No description' }}</div></td>
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Total members</span><div class="mobile-cell-content">{{ $category->members_count }}</div></td>
                                <td role="cell"><span class="mobile-cell-label" aria-hidden="true">Actions</span><div class="mobile-cell-content">
                                    <div class="catalog-actions">
                                        <a class="button button-secondary" href="{{ route('categories.edit', $category) }}">Edit<span class="sr-only"> {{ $category->name }}</span></a>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" data-confirm-form data-confirm-title="Delete {{ $category->name }}?" data-confirm-message="This category will be permanently deleted. Deletion cannot be undone. Assigned members will never be deleted." data-confirm-label="Delete category" data-confirm-note="Only unused categories can be deleted.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button button-danger-outline" type="submit">Delete<span class="sr-only"> {{ $category->name }}</span></button>
                                        </form>
                                    </div>
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($categories->hasPages())
                <div class="pagination-wrap">{{ $categories->links() }}</div>
            @endif
        @endif
    </section>
@endsection
