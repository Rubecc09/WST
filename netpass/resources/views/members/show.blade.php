@extends('layouts.app')

@section('title', $member->full_name)
@section('breadcrumb', 'Member profile')

@section('content')
    <div class="page-heading page-heading-compact profile-page-heading">
        <div>
            <a class="back-link" href="{{ route('members.index') }}"><x-icon name="arrow-left" /> Back to members</a>
            <h1>Member profile</h1>
            <p class="page-description">Review this member’s profile and membership details.</p>
        </div>
        <div class="heading-actions">
            <a class="button button-secondary" href="{{ route('members.index') }}">Back</a>
            <a class="button button-primary" href="{{ route('members.edit', $member) }}"><x-icon name="edit" /> Edit member</a>
        </div>
    </div>

    <section class="profile-card">
        @php
            $initials = collect(explode(' ', $member->full_name))
                ->filter()
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->take(2)
                ->implode('');
        @endphp
        <div class="profile-hero">
            <span class="profile-avatar">{{ mb_strtoupper($initials) }}</span>
            <div class="profile-main">
                <div class="profile-name-row">
                    <h2>{{ $member->full_name }}</h2>
                    <x-member-status :status="$member->status" />
                </div>
                <p class="profile-number">Member ID <span>{{ $member->membership_number }}</span></p>
            </div>
            <div class="profile-membership">
                <span class="profile-membership-label">MEMBERSHIP LEVEL</span>
                <x-membership-level :level="$member->membershipLevel->name" />
            </div>
        </div>

        <div class="profile-content">
            <div class="profile-section-heading">
                <span class="profile-section-icon"><x-icon name="id-card" /></span>
                <div><h3>Personal information</h3><p>Contact details saved to this member profile.</p></div>
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-icon"><x-icon name="mail" /></span>
                    <div><small>Email address</small><a href="mailto:{{ $member->email }}">{{ $member->email }}</a></div>
                </div>
                <div class="detail-item">
                    <span class="detail-icon"><x-icon name="phone" /></span>
                    <div><small>Phone number</small><a href="tel:{{ $member->contact_number }}">{{ $member->contact_number }}</a></div>
                </div>
                <div class="detail-item detail-item-wide">
                    <span class="detail-icon"><x-icon name="map-pin" /></span>
                    <div><small>Address</small><span>{{ $member->address }}</span></div>
                </div>
            </div>

            <div class="profile-section-heading membership-heading">
                <span class="profile-section-icon"><x-icon name="sparkles" /></span>
                <div><h3>Membership information</h3><p>Category and registration details.</p></div>
            </div>
            <div class="detail-grid membership-detail-grid">
                <div class="detail-item">
                    <span class="detail-icon"><x-icon name="users" /></span>
                    <div><small>Membership category</small><span>{{ $member->membershipCategory->name }}</span></div>
                </div>
                <div class="detail-item">
                    <span class="detail-icon"><x-icon name="calendar" /></span>
                    <div><small>Registration date</small><span>{{ $member->registration_date->format('F d, Y') }}</span></div>
                </div>
            </div>
        </div>

        @if ($member->status === 'Active')
            <div class="profile-footer">
                <div><strong>Need to pause this membership?</strong><p>Deactivating retains this profile and its history. It can be reactivated later.</p></div>
                <form action="{{ route('members.deactivate', $member) }}" method="POST" data-confirm-form data-confirm-title="Deactivate {{ $member->full_name }}?" data-confirm-message="This member will no longer be active. Their profile and history will be kept.">
                    @csrf
                    @method('PATCH')
                    <button class="button button-danger-outline" type="submit"><x-icon name="pause" /> Deactivate member</button>
                </form>
            </div>
        @else
            <div class="profile-footer">
                <div><strong>This member is inactive</strong><p>Reactivate their membership to restore active status.</p></div>
                <form action="{{ route('members.reactivate', $member) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="button button-secondary" type="submit"><x-icon name="refresh" /> Reactivate member</button>
                </form>
            </div>
        @endif
    </section>
@endsection
