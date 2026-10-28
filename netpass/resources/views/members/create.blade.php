@extends('layouts.app')

@section('title', 'Add member')
@section('breadcrumb', 'Add member')

@section('content')
    <div class="page-heading page-heading-compact">
        <div>
            <a class="back-link" href="{{ route('members.index') }}"><x-icon name="arrow-left" /> Back to members</a>
            <h1>Add a member</h1>
            <p class="page-description">Create a profile for someone in your café community.</p>
        </div>
    </div>

    @include('members.partials.form', [
        'formTitle' => 'Member information',
        'formDescription' => 'Enter their contact details and membership preferences.',
        'submitLabel' => 'Save member',
        'action' => route('members.store'),
        'method' => 'POST',
        'member' => null,
    ])
@endsection
