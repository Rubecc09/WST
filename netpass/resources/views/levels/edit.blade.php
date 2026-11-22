@extends('layouts.app')
@section('title', 'Edit level')
@section('breadcrumb', 'Membership Levels')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
@endpush
@section('content')
    <div class="page-heading page-heading-compact">
        <div>
            <a class="back-link" href="{{ route('levels.index') }}"><x-icon name="arrow-left" /> Back to levels</a>
            <h1>Edit level</h1>
            <p class="page-description">Configure fees, validity, and benefits for your members.</p>
        </div>
    </div>
    @include('levels.partials.form', ['level' => $level])
@endsection
