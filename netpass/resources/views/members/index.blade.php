@extends('layouts.app')

@section('title', 'Member management')

@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-line"></span> MEMBER DIRECTORY</div>
            <h1>Members</h1>
            <p class="page-description">Manage your café community, all in one place.</p>
        </div>
        <a class="button button-primary" href="{{ route('members.create') }}">
            <x-icon name="plus" /> Add member
        </a>
    </div>

    <section class="summary-grid" aria-label="Member statistics">
        <article class="summary-card">
            <span class="summary-icon summary-icon-blue"><x-icon name="users" /></span>
            <div class="summary-label">Total members</div>
            <div class="summary-value">{{ number_format($totalMembers) }}</div>
            <div class="summary-foot">All registered profiles</div>
        </article>
        <article class="summary-card">
            <span class="summary-icon summary-icon-green"><x-icon name="check" /></span>
            <div class="summary-label">Active members</div>
            <div class="summary-value">{{ number_format($activeMembers) }}</div>
            <div class="summary-foot"><span class="summary-dot dot-green"></span> Currently active</div>
        </article>
        <article class="summary-card">
            <span class="summary-icon summary-icon-gray"><x-icon name="pause" /></span>
            <div class="summary-label">Inactive members</div>
            <div class="summary-value">{{ number_format($inactiveMembers) }}</div>
            <div class="summary-foot">Profiles on hold</div>
        </article>
    </section>

    <section class="directory-card">
        <div class="directory-header">
            <div>
                <h2>Member directory</h2>
                <p>Browse and manage everyone registered with your café.</p>
            </div>
            <span class="record-count">{{ $members->total() }} {{ \Illuminate\Support\Str::plural('member', $members->total()) }}</span>
        </div>

        <form class="filter-bar" action="{{ route('members.index') }}" method="GET">
            <label class="search-box">
                <x-icon name="search" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by member name..." aria-label="Search members by name">
            </label>
            <div class="filter-select-wrap">
                <x-icon name="filter" class="filter-prefix" />
                <select name="category" aria-label="Filter by membership category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-icon name="chevron-down" class="select-chevron" />
            </div>
            <div class="filter-select-wrap">
                <select name="level" aria-label="Filter by membership level">
                    <option value="">All levels</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected((string) ($filters['level'] ?? '') === (string) $level->id)>{{ $level->name }}</option>
                    @endforeach
                </select>
                <x-icon name="chevron-down" class="select-chevron" />
            </div>
            <div class="filter-select-wrap">
                <select name="status" aria-label="Filter by member status">
                    <option value="">Any status</option>
                    <option value="Active" @selected(($filters['status'] ?? '') === 'Active')>Active</option>
                    <option value="Inactive" @selected(($filters['status'] ?? '') === 'Inactive')>Inactive</option>
                </select>
                <x-icon name="chevron-down" class="select-chevron" />
            </div>
            <button type="submit" class="button button-filter">Apply filters</button>
            @if ($hasFilters)
                <a class="clear-filters" href="{{ route('members.index') }}">Clear</a>
            @endif
        </form>

        @if ($members->isNotEmpty())
            <div class="table-scroll">
                <table class="member-table">
                    <thead>
                        <tr>
                            <th scope="col">Member</th>
                            <th scope="col">Category</th>
                            <th scope="col">Level</th>
                            <th scope="col">Status</th>
                            <th scope="col">Joined</th>
                            <th scope="col"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            @php
                                $initials = collect(explode(' ', $member->full_name))
                                    ->filter()
                                    ->map(fn ($part) => mb_substr($part, 0, 1))
                                    ->take(2)
                                    ->implode('');
                            @endphp
                            <tr>
                                <td>
                                    <a class="member-identity" href="{{ route('members.show', $member) }}">
                                        <span class="member-avatar avatar-{{ $loop->index % 5 }}">{{ mb_strtoupper($initials) }}</span>
                                        <span class="member-meta">
                                            <strong>{{ $member->full_name }}</strong>
                                            <small>{{ $member->email }}</small>
                                        </span>
                                    </a>
                                </td>
                                <td><span class="category-name">{{ $member->membershipCategory->name }}</span></td>
                                <td><x-membership-level :level="$member->membershipLevel->name" /></td>
                                <td><x-member-status :status="$member->status" /></td>
                                <td class="joined-date">{{ $member->registration_date->format('M d, Y') }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a class="icon-action" href="{{ route('members.show', $member) }}" aria-label="View {{ $member->full_name }}" title="View member"><x-icon name="eye" /></a>
                                        <a class="icon-action" href="{{ route('members.edit', $member) }}" aria-label="Edit {{ $member->full_name }}" title="Edit member"><x-icon name="edit" /></a>
                                        @if ($member->status === 'Active')
                                            <form action="{{ route('members.deactivate', $member) }}" method="POST" data-confirm-form data-confirm-title="Deactivate {{ $member->full_name }}?" data-confirm-message="This member will no longer be active. Their profile and history will be kept.">
                                                @csrf
                                                @method('PATCH')
                                                <button class="icon-action icon-action-danger" type="submit" aria-label="Deactivate {{ $member->full_name }}" title="Deactivate member"><x-icon name="pause" /></button>
                                            </form>
                                        @else
                                            <form action="{{ route('members.reactivate', $member) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="icon-action icon-action-success" type="submit" aria-label="Reactivate {{ $member->full_name }}" title="Reactivate member"><x-icon name="refresh" /></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">
                <span class="pagination-summary">Showing <strong>{{ $members->firstItem() }}–{{ $members->lastItem() }}</strong> of <strong>{{ $members->total() }}</strong> members</span>
                {{ $members->onEachSide(1)->links() }}
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon"><x-icon name="users" /></span>
                <h3>{{ $totalMembers === 0 ? 'Your member list is ready' : 'No members found' }}</h3>
                <p>
                    @if ($totalMembers === 0)
                        Add your first member to start building your café community.
                    @else
                        Try adjusting your search or filters to find what you’re looking for.
                    @endif
                </p>
                @if ($totalMembers === 0)
                    <a class="button button-primary" href="{{ route('members.create') }}"><x-icon name="plus" /> Add your first member</a>
                @elseif ($hasFilters)
                    <a class="button button-secondary" href="{{ route('members.index') }}">Clear filters</a>
                @endif
            </div>
        @endif
    </section>
@endsection
