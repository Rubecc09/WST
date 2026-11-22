@extends('layouts.app')

@section('title', 'Edit member')
@section('breadcrumb', 'Edit member')

@section('content')
    <div class="page-heading page-heading-compact">
        <div>
            <a class="back-link" href="{{ route('members.index') }}"><x-icon name="arrow-left" /> Back to members</a>
            <h1>Edit member</h1>
            <p class="page-description">Update {{ $member->full_name }}’s profile and membership details.</p>
        </div>
    </div>

    @include('members.partials.form', [
        'formTitle' => 'Member information',
        'formDescription' => 'Update contact details and membership preferences.',
        'submitLabel' => 'Save changes',
        'action' => route('members.update', $member),
        'method' => 'PUT',
        'member' => $member,
    ])
@endsection
