@extends('layouts.app')
@section('title', 'Edit category')
@section('breadcrumb', 'Membership Categories')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
@endpush
@section('content')
    <div class="page-heading page-heading-compact">
        <div>
            <a class="back-link" href="{{ route('categories.index') }}"><x-icon name="arrow-left" /> Back to categories</a>
            <h1>Edit category</h1>
            <p class="page-description">Organize your café community into membership categories.</p>
        </div>
    </div>
    @include('categories.partials.form', ['category' => $category])
@endsection
